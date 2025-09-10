<?php

namespace App\Livewire\Notification;

use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public ?string $status = null;

    public function updatingStatus(){ $this->resetPage(); }

    public function render()
    {
        $q = Notification::with('receiver','transfer')->latest();
        if ($this->status) $q->where('status',$this->status);

        $rows = $q->paginate(10);
        return view('livewire.notification.table', compact('rows'));
    }
}
