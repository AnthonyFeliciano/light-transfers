<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?string $role = null;

    public string $search = '';
    public int $perPage = 10;

    protected $queryString = [
        'role'    => ['except' => ''],
        'search'  => ['except' => ''],
        'page'    => ['except' => 1],
        'perPage' => ['except' => 10],
    ];

    public function updatingRole()    { $this->resetPage(); }
    public function updatingSearch()  { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function render()
    {
        $q = User::query()->orderBy('name');

        if ($this->role) $q->where('role',$this->role);

        if (filled($this->search)) {
            $term = '%' . str_replace(' ', '%', $this->search) . '%';
            $q->where(function ($sub) use ($term) {
                $sub->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return view('livewire.users.index', [
            'rows' => $q->paginate($this->perPage)->withQueryString(),
        ]);
    }
}
