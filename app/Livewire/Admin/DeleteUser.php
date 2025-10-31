<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Routing\Redirector;
use Livewire\Component;

class DeleteUser extends Component
{

    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;

    }
    public function render()
    {
        return view('livewire.admin.delete-user');
    }

    public function delete(): Redirector
    {
        $this->authorize('delete', $this->user);
        $this->user->delete();
        session()->flash('success', 'User has been deleted successfully.');
        return redirect()->route('admin.users.index');
    }
}
