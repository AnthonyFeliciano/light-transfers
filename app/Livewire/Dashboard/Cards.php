<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Cards extends Component
{
    public float $balance = 0.0;

    // quando a transferência concluir, o Wizard emite 'wallet-updated'
    protected $listeners = ['wallet-updated' => 'reload'];

    public function mount(): void
    {
        $this->reload();
    }

    /** Recarrega o saldo do usuário logado */
    public function reload(): void
    {
        $user = Auth::user();
        // se você usa decimal(18,2):
        $this->balance = (float) ($user->wallet?->balance ?? 0);

        // se você usar centavos (int), troque por:
        // $this->balance = (float) (($user->wallet?->balance_cents ?? 0) / 100);
    }

    public function render()
    {
        return view('livewire.dashboard.cards');
    }
}
