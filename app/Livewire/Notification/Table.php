<?php

namespace App\Livewire\Notification;

use App\Models\Notification;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    #[Url(as: 'status', history: true)]
    public ?string $status = null;

    #[Url(as: 'page', history: true)]
    public int $page = 1;

    public function updatingStatus() { $this->resetPage(); }

    public function render()
    {
        $q = Notification::with('receiver','transfer')->latest();

        if ($this->status) {
            $q->where('status', $this->status);
        }

        return view('livewire.notification.table', [
            'rows' => $q->paginate(10), // sem withQueryString (Livewire controla via Url attrs)
        ]);
    }
}
