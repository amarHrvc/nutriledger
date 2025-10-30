<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditUser extends Component
{
    // The user being edited (loaded from route parameter)
    public User $user;
    
    // Form properties (public = available in view)
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = '';

    /**
     * LIFECYCLE HOOK: Mount runs when component initializes
     * Similar to __construct() but for Livewire
     * Route model binding injects $user automatically
     */
    public function mount(User $user)
    {
        // Populate form with existing user data
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        // Note: We DON'T populate password (security)
    }

    /**
     * DYNAMIC VALIDATION RULES
     * Using a method instead of property allows dynamic rules
     */
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Rule::ignore() excludes current user from unique check
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            // Password is NULLABLE on edit (only update if provided)
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,doktor,pacijent'],
        ];
    }

    /**
     * REAL-TIME VALIDATION (Optional)
     * Validates field as user types
     */
    public function updatedName()
    {
        $this->validateOnly('name');
    }

    public function updatedEmail()
    {
        $this->validateOnly('email');
    }

    /**
     * Save/Update the user
     */
    public function save()
    {
        // Validate all fields
        $validated = $this->validate();

        // Prepare update data
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Update user
        $this->user->update($updateData);

        // Flash success message to session
        session()->flash('success', 'User updated successfully.');

        // Redirect back to user list
        return $this->redirect(route('admin.users.index'), navigate: true);
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.edit-user');
    }
}
