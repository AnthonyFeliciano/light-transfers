<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'role', history: true)]
    public ?string $role = null;

    #[Url(as: 'search', history: true)]
    public string $search = '';

    #[Url(as: 'perPage', history: true)]
    public int $perPage = 10;

    #[Url(as: 'page', history: true)]
    public int $page = 1;

    public function updatingRole()    { $this->resetPage(); }
    public function updatingSearch()  { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function render()
    {
        $q = User::query()->orderBy('name');

        if ($this->role) {
            $q->where('role', $this->role);
        }

        if (filled($this->search)) {
            $term = '%' . str_replace(' ', '%', $this->search) . '%';
            $q->where(fn($sub) =>
                $sub->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
            );
        }

        return view('livewire.users.index', [
            'rows' => $q->paginate($this->perPage), // <- sem withQueryString
        ]);
    }
}
