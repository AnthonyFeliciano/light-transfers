<?php

namespace Tests\Feature\Transfer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Models\User;
use App\Models\Wallet;
use App\Models\LedgerEntry;
use App\Services\Contracts\AuthorizationClientContract;
use App\Services\Contracts\TransferServiceContract;     

class TransferInsufficientFundsTest extends TestCase
{
    use RefreshDatabase;

    public function test_rollback_quando_saldo_insuficiente_preserva_carteiras_e_nao_cria_ledger(): void
    {
        $payer = User::factory()->create(['role' => 'user']);
        $payee = User::factory()->create(['role' => 'user']);

        Wallet::factory()->create(['user_id' => $payer->id, 'balance' => '5.00']);
        Wallet::factory()->create(['user_id' => $payee->id, 'balance' => '0.00']);

        app()->bind(AuthorizationClientContract::class, fn () => new class implements AuthorizationClientContract {
            public function authorize(): bool { return true; }
        });
   
        $service = app(TransferServiceContract::class);

        try {
            $service->execute($payer->id, $payee->id, '10.00', (string) Str::uuid());
            $this->fail('Era para lançar ValidationException');
        } catch (ValidationException $e) {
            $fmt = fn ($v) => number_format((float) $v, 2, '.', '');

            $this->assertSame('5.00', $fmt(Wallet::where('user_id', $payer->id)->value('balance')));
            $this->assertSame('0.00', $fmt(Wallet::where('user_id', $payee->id)->value('balance')));

            $this->assertSame(0, LedgerEntry::count());
        }
    }
}
