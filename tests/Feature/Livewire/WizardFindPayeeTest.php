<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

use App\Livewire\Transfer\Wizard;
use App\Models\User;

class WizardFindPayeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_findpayee_por_cpf_com_mascara_encontra_destinatario_define_payeeid_e_step_2(): void
    {
        $me   = User::factory()->create(['role' => 'user', 'document' => '12345678901']);
        $dest = User::factory()->create(['role' => 'user', 'document' => '09452112956']);

        Auth::login($me);

        Livewire::test(Wizard::class)
            ->set('identifier', '094.521.129-56')
            ->call('findPayee')
            ->assertSet('payeeId', $dest->id)
            ->assertSet('step', 2);
    }
}
