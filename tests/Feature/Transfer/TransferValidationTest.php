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

class TransferValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->bind(AuthorizationClientContract::class, fn () => new class implements AuthorizationClientContract {
            public function authorize(): bool { return true; }
        });
    }

    public function test_impede_transferir_para_si_mesmo(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Wallet::factory()->create(['user_id' => $user->id, 'balance' => '50.00']);

        $service = app(TransferServiceContract::class);

        $this->expectException(ValidationException::class);
        $service->execute($user->id, $user->id, '10.00', (string) Str::uuid());
    }

    public function test_lojista_nao_envia_transferencias(): void
    {
        $merchant = User::factory()->create(['role' => 'merchant']);
        $user     = User::factory()->create(['role' => 'user']);

        Wallet::factory()->create(['user_id' => $merchant->id, 'balance' => '50.00']);
        Wallet::factory()->create(['user_id' => $user->id, 'balance' => '0.00']);

        $service = app(TransferServiceContract::class);

        $this->expectException(ValidationException::class);
        $service->execute($merchant->id, $user->id, '10.00', (string) Str::uuid());
    }
}
