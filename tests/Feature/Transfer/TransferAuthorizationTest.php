<?php

namespace Tests\Feature\Transfer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Models\User;
use App\Models\Wallet;
use App\Services\Contracts\AuthorizationClientContract;
use App\Services\Contracts\TransferServiceContract;

class TransferAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bloqueia_quando_autorizador_externo_nega_lanca_validation_exception_sem_side_effects(): void
    {
        $payer = User::factory()->create(['role' => 'user']);
        $payee = User::factory()->create(['role' => 'user']);

        Wallet::factory()->create(['user_id' => $payer->id, 'balance' => '100.00']);
        Wallet::factory()->create(['user_id' => $payee->id, 'balance' => '0.00']);

        // Autorizador sempre FALSE
        app()->bind(AuthorizationClientContract::class, fn () => new class implements AuthorizationClientContract {
            public function authorize(): bool { return false; }
        });

        $service = app(TransferServiceContract::class); 

        try {
            $service->execute($payer->id, $payee->id, '10.00', (string) Str::uuid());
            $this->fail('Era para lançar ValidationException');
        } catch (ValidationException $e) {
            // Saldos preservados
            $fmt = fn ($v) => number_format((float)$v, 2, '.', '');
            $this->assertSame('100.00', $fmt(Wallet::where('user_id', $payer->id)->value('balance')));
            $this->assertSame('0.00',   $fmt(Wallet::where('user_id', $payee->id)->value('balance')));
        }
    }
}
