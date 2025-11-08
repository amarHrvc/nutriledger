<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Log;

class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';

    /**
     * Render the component.
     */
    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }

    /**
     * Reset pagination when search or filter changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }


    public function updatedRoleFilter()
    {
        LOG::debug($this->roleFilter);
    }

    /**
     * Delete a user.
     */
    public function deleteUser(int $userId)
    {
        $user = User::findOrFail($userId);

        $this->authorize('delete', $user);

        $user->delete();

        session()->flash('success', 'User deleted successfully.');

        $this->resetPage();

        FLUX::modal('delete-user-' . $user->id)->close();
    }
}
