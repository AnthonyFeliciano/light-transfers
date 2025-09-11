<?php

namespace Tests\Feature\Transfer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

use App\Jobs\SendTransferNotificationJob;
use App\Models\User;
use App\Models\Wallet;
use App\Models\LedgerEntry;
use App\Models\Transfer;
use App\Services\Contracts\AuthorizationClientContract;
use App\Services\Contracts\TransferServiceContract;

class TransferIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_e_idempotente_por_key_segunda_chamada_retorna_a_mesma_transfer_sem_side_effects(): void
    {
        Queue::fake();

        // Arrange
        $payer = User::factory()->create(['role' => 'user']);
        $payee = User::factory()->create(['role' => 'user']);

        Wallet::factory()->create(['user_id' => $payer->id, 'balance' => '100.00']);
        Wallet::factory()->create(['user_id' => $payee->id, 'balance' => '0.00']);

        app()->bind(AuthorizationClientContract::class, fn () => new class implements AuthorizationClientContract {
            public function authorize(): bool { return true; }
        });

        $service = app(TransferServiceContract::class);

        $key = (string) Str::uuid();

        $t1 = $service->execute($payer->id, $payee->id, '10.00', $key);
        $t2 = $service->execute($payer->id, $payee->id, '10.00', $key);

        $this->assertSame($t1->id, $t2->id);

        // Saldos só devem refletir UMA transferência
        $fmt = fn ($v) => number_format((float) $v, 2, '.', '');
        $this->assertSame('90.00', $fmt(Wallet::where('user_id', $payer->id)->value('balance')));
        $this->assertSame('10.00', $fmt(Wallet::where('user_id', $payee->id)->value('balance')));

        // (Opcional) Ledger: apenas 2 lançamentos (DEBIT/CREDIT) para essa transferência
        $this->assertSame(
            2,
            LedgerEntry::where('transfer_id', $t1->id)->count(),
            'Deve haver exatamente 2 lançamentos no razão para a transferência (DEBIT/CREDIT).'
        );

        Queue::assertPushed(SendTransferNotificationJob::class, 1);

        $this->assertSame(
            1,
            Transfer::where('id', $t1->id)->count(),
            'A key idempotente não deve criar outra transferência.'
        );
    }
}
