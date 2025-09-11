<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Livewire;

use App\Livewire\Transfer\Wizard;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Contracts\AuthorizationClientContract;

class WizardConfirmFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_autorizado_executa_servico_reseta_estado_e_atualiza_available(): void
    {
        /** @var User $me */
        $me   = User::factory()->createOne(['role' => 'user']);
        Wallet::factory()->createOne(['user_id' => $me->id, 'balance' => '100.00']);

        /** @var User $dest */
        $dest = User::factory()->createOne(['role' => 'user']);
        // ➜ IMPORTANTE: wallet do recebedor
        Wallet::factory()->createOne(['user_id' => $dest->id, 'balance' => '0.00']);

        Auth::login($me);

        // Autorizador TRUE
        app()->bind(AuthorizationClientContract::class, fn () => new class implements AuthorizationClientContract {
            public function authorize(): bool { return true; }
        });

        Livewire::test(Wizard::class)
            ->set('payeeId', $dest->id)
            ->set('amount', '10.00')
            ->set('key', (string) Str::uuid())
            ->call('confirm')
            ->assertSet('lastToast.type', 'success')
            ->assertSet('identifier', '')
            ->assertSet('amount', '')
            ->assertSet('payeeId', null)
            ->assertSet('step', 1)
            ->assertSet('available', '90.00'); // 100 → 90
    }

    public function test_confirm_nao_autorizado_nao_chama_servico_e_mostra_erro(): void
    {
        /** @var User $me */
        $me   = User::factory()->createOne(['role' => 'user']);
        Wallet::factory()->createOne(['user_id' => $me->id, 'balance' => '100.00']);

        /** @var User $dest */
        $dest = User::factory()->createOne(['role' => 'user']);
        // ➜ Também cria a wallet do recebedor
        Wallet::factory()->createOne(['user_id' => $dest->id, 'balance' => '0.00']);

        Auth::login($me);

        // Autorizador FALSE
        app()->bind(AuthorizationClientContract::class, fn () => new class implements AuthorizationClientContract {
            public function authorize(): bool { return false; }
        });

        $msg = 'Não conseguimos autorização para esta operação agora. Tente novamente em instantes.';

        Livewire::test(Wizard::class)
            ->set('payeeId', $dest->id)
            ->set('amount', '10.00')
            ->call('confirm')
            ->assertSet('lastToast.type', 'error')
            ->assertSet('lastToast.msg', $msg)
            ->assertSet('authMsg', $msg);
    }
}
