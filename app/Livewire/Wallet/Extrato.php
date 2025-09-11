<?php

namespace App\Livewire\Wallet;

use App\Models\LedgerEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Extrato extends Component
{
    use WithPagination;

    #[Url(as: 'type', history: true)]
    public ?string $type = null;

    #[Url(as: 'from', history: true)]
    public ?string $from = null;

    #[Url(as: 'to', history: true)]
    public ?string $to = null;

    #[Url(as: 'perPage', history: true)]
    public int $perPage = 10;

    #[Url(as: 'page', history: true)]
    public int $page = 1;

    public function updatingType()    { $this->resetPage(); }
    public function updatingFrom()    { $this->resetPage(); }
    public function updatingTo()      { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function render()
    {
        $wallet   = Auth::user()?->wallet;
        $balance  = (float) ($wallet->balance ?? 0);
        $walletId = $wallet?->id;

        $q = LedgerEntry::query()
            ->when($walletId, fn ($qq) => $qq->where('wallet_id', $walletId))
            ->with(['transfer:id,payer_id,payee_id'])
            ->orderByDesc('created_at');

        if ($this->type) {
            $q->where('type', strtoupper($this->type));
        }

        if ($this->from) $q->whereDate('created_at', '>=', $this->from);
        if ($this->to)   $q->whereDate('created_at', '<=', $this->to);

        return view('livewire.wallet.extrato', [
            'balance' => $balance,
            'rows'    => $q->paginate($this->perPage), // Livewire cuida dos params via Url attrs
        ]);
    }
}
