<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

use App\Livewire\Transfer\Wizard;
use App\Models\User;
use App\Models\Wallet;

class WizardValidateAmountTest extends TestCase
{
    use RefreshDatabase;

    public function test_validate_amount_com_saldo_suficiente_avanca_para_step_3(): void
    {
        $me = User::factory()->create(['role' => 'user']);
        Wallet::factory()->create(['user_id' => $me->id, 'balance' => '50.00']);
        Auth::login($me);

        Livewire::test(Wizard::class)
            ->set('amount', '10.00')
            ->call('validateAmount')
            ->assertSet('step', 3)
            ->assertHasNoErrors();
    }

    public function test_validate_amount_com_saldo_insuficiente_mantem_step_e_mostra_erro_em_amount(): void
    {
        $me = User::factory()->create(['role' => 'user']);
        Wallet::factory()->create(['user_id' => $me->id, 'balance' => '5.00']);
        Auth::login($me);

        Livewire::test(Wizard::class)
            ->set('amount', '10.00')
            ->call('validateAmount')
            ->assertHasErrors(['amount']); 
    }
}
