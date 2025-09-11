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
use App\Models\Notification;
use App\Services\Contracts\AuthorizationClientContract;
use App\Services\Contracts\TransferServiceContract;

class TransferHappyPathTest extends TestCase
{
    use RefreshDatabase;

    public function test_completa_transferencia_user_para_merchant_atualiza_saldos_cria_ledger_e_notification_pending(): void
    {
        Queue::fake();

        $payer = User::factory()->create(['role' => 'user']);
        $payee = User::factory()->create(['role' => 'merchant']);

        Wallet::factory()->create(['user_id' => $payer->id, 'balance' => '100.00']);
        Wallet::factory()->create(['user_id' => $payee->id, 'balance' => '0.00']);

        // Autorizador sempre true
        app()->bind(AuthorizationClientContract::class, fn () => new class implements AuthorizationClientContract {
            public function authorize(): bool { return true; }
        });

        $service  = app(TransferServiceContract::class); 
        $transfer = $service->execute($payer->id, $payee->id, '10.00', key: (string) Str::uuid());

        $this->assertSame('COMPLETED', strtoupper($transfer->status));

        $fmt = fn ($v) => number_format((float) $v, 2, '.', '');
        $this->assertSame('90.00', $fmt(Wallet::where('user_id', $payer->id)->value('balance')));
        $this->assertSame('10.00', $fmt(Wallet::where('user_id', $payee->id)->value('balance')));

        $this->assertTrue(
            LedgerEntry::where('transfer_id', $transfer->id)
                ->whereRaw('UPPER(type) = ?', ['DEBIT'])
                ->exists()
        );
        $this->assertTrue(
            LedgerEntry::where('transfer_id', $transfer->id)
                ->whereRaw('UPPER(type) = ?', ['CREDIT'])
                ->exists()
        );

        $notification = Notification::where('transfer_id', $transfer->id)->first();
        $this->assertNotNull($notification);
        $this->assertSame('pending', strtolower($notification->status));

        Queue::assertPushed(SendTransferNotificationJob::class, function ($job) use ($notification) {
            return $job->notificationId === $notification->id;
        });
    }
}
