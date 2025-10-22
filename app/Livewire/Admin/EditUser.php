<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class EditUser extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.edit-user');
    }
}
