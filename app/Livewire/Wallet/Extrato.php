<?php

namespace App\Livewire\Wallet;

use App\Models\LedgerEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Extrato extends Component
{
    use WithPagination;

    public ?string $type = null;
    public ?string $from = null;
    public ?string $to   = null;
    public int $perPage  = 10;

    protected $queryString = [
        'type'    => ['except' => ''],
        'from'    => ['except' => ''],
        'to'      => ['except' => ''],
        'page'    => ['except' => 1],
        'perPage' => ['except' => 10],
    ];

    public function updatingType()    { $this->resetPage(); }
    public function updatingFrom()    { $this->resetPage(); }
    public function updatingTo()      { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function render()
    {
        $user     = Auth::user();
        $wallet   = $user?->wallet;
        $balance  = (float) ($wallet->balance ?? 0);
        $walletId = $wallet?->id;

        $q = LedgerEntry::query()
            ->when($walletId, fn ($qq) => $qq->where('wallet_id', $walletId))
            ->with(['transfer:id,payer_id,payee_id']) // <- precisaremos pra saber direção
            ->orderByDesc('created_at');

        if ($this->type) {
            $q->where('type', $this->type);
        }

        if ($this->from) {
            $q->whereDate('created_at', '>=', $this->from);
        }
        if ($this->to) {
            $q->whereDate('created_at', '<=', $this->to);
        }

        return view('livewire.wallet.extrato', [
            'balance' => $balance,
            'rows'    => $q->paginate($this->perPage)->withQueryString(),
        ]);
    }
}
