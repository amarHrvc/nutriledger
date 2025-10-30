<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CreateUser extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'pacijent';

    protected array $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'role' => ['required', 'in:admin,doktor,pacijent'],
    ];

    /**
     * Save the user.
     */
    public function save()
    {
        // Livewire doesn't re-apply route middleware with arguments (role:admin)
        // So we need explicit authorization here
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        session()->flash('success', 'User created successfully.');

        return $this->redirect(route('admin.users.index'), navigate: true);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.create-user');
    }
}
