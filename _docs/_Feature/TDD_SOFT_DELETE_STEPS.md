# TDD Steps to Implement Soft Delete for Users in Livewire

## Overview
This guide provides a step-by-step approach to implementing soft delete functionality for users using Test-Driven Development (TDD) principles in a Laravel Livewire application.

**Current State:**
- User Management system with CreateUser, EditUser, and UserManagement components
- Placeholder tests exist for delete functionality (currently skipped)
- Users table uses SQLite database
- Authorization system in place (admin, doktor, pacijent roles)

**Goal:**
- Implement soft delete for users (mark as deleted, don't remove from database)
- Follow TDD approach (write tests first, then implementation)
- Ensure proper authorization (only admin can delete, cannot delete self)

---

## Step 1: Add SoftDeletes to User Model

### What
Enable soft deletes on the User model

### Why
Allows marking users as deleted without removing them from the database. This is useful for:
- Data retention and audit trails
- Ability to restore users if needed
- Maintaining referential integrity with related records

### Actions Required

#### 1.1 Create Migration
```bash
php artisan make:migration add_soft_deletes_to_users_table
```

**Migration content:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
```

#### 1.2 Run Migration
```bash
php artisan migrate
```

#### 1.3 Update User Model
Add the `SoftDeletes` trait to `app/Models/User.php`:

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;
    
    // ... rest of the model
}
```

### Verification
- Check database: `deleted_at` column should exist in users table
- Test in tinker: `User::withTrashed()->get()` should work

---

## Step 2: Write Failing Tests (Red Phase)

### What
Write comprehensive tests for delete functionality before implementing the feature

### Why
TDD approach ensures:
- Clear requirements definition
- All edge cases are considered
- Code is testable by design
- Regression prevention

### Test Cases to Implement

#### 2.1 Basic Delete Functionality
**File:** `tests/Feature/Admin/UserManagementTest.php`

```php
test('admin can soft delete a user', function () {
    $this->actingAs($this->admin);
    
    $userToDelete = User::factory()->create(['role' => 'pacijent']);
    
    Livewire::test(\App\Livewire\Admin\UserManagement::class)
        ->call('delete', $userToDelete->id)
        ->assertSessionHas('success');
    
    // User should be soft deleted
    $this->assertSoftDeleted('users', [
        'id' => $userToDelete->id,
    ]);
    
    // User should not appear in normal queries
    expect(User::find($userToDelete->id))->toBeNull();
    
    // User should exist when including trashed
    expect(User::withTrashed()->find($userToDelete->id))->not->toBeNull();
});
```

#### 2.2 Self-Deletion Prevention
```php
test('admin cannot delete themselves', function () {
    $this->actingAs($this->admin);
    
    Livewire::test(\App\Livewire\Admin\UserManagement::class)
        ->call('delete', $this->admin->id)
        ->assertHasErrors()
        ->assertSessionHas('error');
    
    // Admin should still exist
    $this->assertDatabaseHas('users', [
        'id' => $this->admin->id,
        'deleted_at' => null,
    ]);
});
```

#### 2.3 Soft Deleted Users Don't Appear in List
```php
test('soft deleted users do not appear in user list', function () {
    $activeUser = User::factory()->create(['name' => 'Active User']);
    $deletedUser = User::factory()->create(['name' => 'Deleted User']);
    $deletedUser->delete(); // Soft delete
    
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Admin\UserManagement::class)
        ->assertSee('Active User')
        ->assertDontSee('Deleted User');
});
```

#### 2.4 Authorization Tests
```php
test('doktor cannot delete users', function () {
    $userToDelete = User::factory()->create(['role' => 'pacijent']);
    
    Livewire::actingAs($this->doktor)
        ->test(\App\Livewire\Admin\UserManagement::class)
        ->call('delete', $userToDelete->id)
        ->assertForbidden();
    
    // User should still exist
    $this->assertDatabaseHas('users', [
        'id' => $userToDelete->id,
        'deleted_at' => null,
    ]);
});

test('pacijent cannot delete users', function () {
    $userToDelete = User::factory()->create(['role' => 'doktor']);
    
    Livewire::actingAs($this->pacijent)
        ->test(\App\Livewire\Admin\UserManagement::class)
        ->call('delete', $userToDelete->id)
        ->assertForbidden();
    
    // User should still exist
    $this->assertDatabaseHas('users', [
        'id' => $userToDelete->id,
        'deleted_at' => null,
    ]);
});
```

#### 2.5 Optional: Restore Functionality (Future)
```php
test('admin can restore soft deleted user', function () {
    $this->markTestSkipped('Restore functionality - future implementation');
    
    $deletedUser = User::factory()->create();
    $deletedUser->delete();
    
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Admin\UserManagement::class)
        ->call('restore', $deletedUser->id)
        ->assertSessionHas('success');
    
    $this->assertDatabaseHas('users', [
        'id' => $deletedUser->id,
        'deleted_at' => null,
    ]);
});
```

### Running Tests
```bash
php artisan test --filter=UserManagementTest
```

**Expected Result:** All new tests should FAIL (Red phase)

---

## Step 3: Create DeleteUser Livewire Component (Option A)

### What
Create a dedicated Livewire component to handle user deletion

### Why
- Separation of concerns
- Reusable delete logic
- Can be used as modal component
- Follows single responsibility principle

### Implementation

#### 3.1 Generate Component
```bash
php artisan make:livewire Admin/DeleteUser
```

#### 3.2 Component Logic
**File:** `app/Livewire/Admin/DeleteUser.php`

```php
<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DeleteUser extends Component
{
    use AuthorizesRequests;
    
    public User $user;
    public bool $showConfirmation = false;
    
    public function mount(User $user)
    {
        $this->user = $user;
    }
    
    public function confirmDelete()
    {
        $this->authorize('delete', $this->user);
        $this->showConfirmation = true;
    }
    
    public function delete()
    {
        $this->authorize('delete', $this->user);
        
        // Prevent self-deletion
        if ($this->user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete yourself.');
            return;
        }
        
        $this->user->delete();
        
        session()->flash('success', 'User deleted successfully.');
        
        return redirect()->route('admin.users.index');
    }
    
    public function cancel()
    {
        $this->showConfirmation = false;
    }
    
    public function render()
    {
        return view('livewire.admin.delete-user');
    }
}
```

#### 3.3 Component View
**File:** `resources/views/livewire/admin/delete-user.blade.php`

```blade
<div>
    @if($showConfirmation)
        <!-- Confirmation Modal -->
        <flux:modal wire:model="showConfirmation">
            <flux:heading>Delete User</flux:heading>
            
            <flux:text>
                Are you sure you want to delete <strong>{{ $user->name }}</strong>?
                This action can be undone later.
            </flux:text>
            
            <flux:button variant="danger" wire:click="delete">
                Delete User
            </flux:button>
            
            <flux:button variant="ghost" wire:click="cancel">
                Cancel
            </flux:button>
        </flux:modal>
    @else
        <flux:button 
            variant="danger" 
            size="sm" 
            wire:click="confirmDelete"
        >
            Delete
        </flux:button>
    @endif
</div>
```

### Pros/Cons

**Pros:**
- Dedicated component for delete logic
- Easy to test in isolation
- Can be used as a modal
- Reusable

**Cons:**
- More files to maintain
- Slightly more complex setup
- Need to pass user to component

---

## Step 4: Add Delete Method to UserManagement Component (Option B)

### What
Add delete functionality directly to the existing UserManagement component

### Why
- Simpler approach
- Fewer files to maintain
- All user management in one place
- Easier to understand flow

### Implementation

#### 4.1 Update UserManagement Component
**File:** `app/Livewire/Admin/UserManagement.php`

Add these properties and methods:

```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserManagement extends Component
{
    use WithPagination, AuthorizesRequests;
    
    public string $search = '';
    public string $roleFilter = '';
    
    // Add these properties
    public ?int $userToDelete = null;
    public bool $showDeleteConfirmation = false;
    
    // ... existing methods ...
    
    public function confirmDelete(int $userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);
        
        $this->userToDelete = $userId;
        $this->showDeleteConfirmation = true;
    }
    
    public function delete()
    {
        if (!$this->userToDelete) {
            return;
        }
        
        $user = User::findOrFail($this->userToDelete);
        $this->authorize('delete', $user);
        
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete yourself.');
            $this->cancelDelete();
            return;
        }
        
        $user->delete();
        
        session()->flash('success', "User '{$user->name}' has been deleted successfully.");
        
        $this->cancelDelete();
        $this->resetPage();
    }
    
    public function cancelDelete()
    {
        $this->userToDelete = null;
        $this->showDeleteConfirmation = false;
    }
}
```

#### 4.2 Update View
**File:** `resources/views/livewire/admin/user-management.blade.php`

Add delete button to each user row and confirmation modal:

```blade
<!-- In the users table, add delete button -->
<td>
    <div class="flex gap-2">
        <flux:button 
            variant="primary" 
            size="sm"
            href="{{ route('admin.users.edit', $user->id) }}"
        >
            Edit
        </flux:button>
        
        <flux:button 
            variant="danger" 
            size="sm"
            wire:click="confirmDelete({{ $user->id }})"
        >
            Delete
        </flux:button>
    </div>
</td>

<!-- Add confirmation modal at the bottom of the view -->
@if($showDeleteConfirmation && $userToDelete)
    @php
        $user = \App\Models\User::find($userToDelete);
    @endphp
    
    <flux:modal wire:model="showDeleteConfirmation">
        <flux:heading>Delete User</flux:heading>
        
        <flux:text>
            Are you sure you want to delete <strong>{{ $user->name }}</strong>?
            This action can be undone later.
        </flux:text>
        
        <div class="flex gap-2">
            <flux:button 
                variant="danger" 
                wire:click="delete"
            >
                Delete User
            </flux:button>
            
            <flux:button 
                variant="ghost" 
                wire:click="cancelDelete"
            >
                Cancel
            </flux:button>
        </div>
    </flux:modal>
@endif
```

### Pros/Cons

**Pros:**
- Simpler implementation
- All logic in one place
- Fewer files
- Easier to maintain

**Cons:**
- Component grows larger
- Less reusable
- Mixing concerns

---

## Step 5: Update User Queries to Exclude Soft Deleted

### What
Ensure soft deleted users don't appear in standard user lists

### Why
Users expect deleted items to disappear from lists. Soft deletes automatically handle this, but we need to verify.

### Verification

The `SoftDeletes` trait automatically excludes soft deleted records from queries. Your existing query in `UserManagement::render()` will automatically work:

```php
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
```

### Optional: Show Trashed Users

If you want to show deleted users in a separate view:

```php
// Show only trashed
$trashedUsers = User::onlyTrashed()->get();

// Show all including trashed
$allUsers = User::withTrashed()->get();

// Check if user is trashed
if ($user->trashed()) {
    // User is soft deleted
}
```

---

## Step 6: Add Delete UI (Button with Confirmation)

### What
Add user interface elements for delete functionality

### Why
Users need a visual way to trigger deletion with proper feedback and confirmation

### UI Components Needed

#### 6.1 Delete Button
- Visual indicator (danger/red color)
- Appropriate size
- Clear label
- Loading state

#### 6.2 Confirmation Dialog
- Modal or native confirm
- Clear message
- Show username
- Cancel option
- Destructive action emphasis

#### 6.3 Feedback Messages
- Success toast/flash message
- Error message for self-deletion
- Loading indicator during delete

### Implementation Examples

#### Using Flux UI Modal (Recommended)
See Step 4.2 above for modal implementation

#### Using JavaScript Confirm (Simple)
```blade
<flux:button 
    variant="danger" 
    size="sm"
    wire:click="delete({{ $user->id }})"
    wire:confirm="Are you sure you want to delete {{ $user->name }}?"
>
    Delete
</flux:button>
```

#### Using Alpine.js
```blade
<div x-data="{ showConfirm: false }">
    <flux:button 
        variant="danger" 
        size="sm"
        @click="showConfirm = true"
    >
        Delete
    </flux:button>
    
    <div x-show="showConfirm" 
         x-cloak
         class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center"
    >
        <div class="bg-white p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Delete User</h3>
            <p class="mb-4">Are you sure you want to delete {{ $user->name }}?</p>
            
            <div class="flex gap-2">
                <button 
                    wire:click="delete({{ $user->id }})"
                    @click="showConfirm = false"
                    class="px-4 py-2 bg-red-600 text-white rounded"
                >
                    Delete
                </button>
                
                <button 
                    @click="showConfirm = false"
                    class="px-4 py-2 bg-gray-300 rounded"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## Step 7: Add Authorization Policy (Recommended)

### What
Create a UserPolicy to centralize authorization logic

### Why
- Single source of truth for permissions
- Reusable across application
- Easier to maintain
- Better security

### Implementation

#### 7.1 Generate Policy
```bash
php artisan make:policy UserPolicy --model=User
```

#### 7.2 Define Policy
**File:** `app/Policies/UserPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admins can delete
        if (!$user->isAdmin()) {
            return false;
        }
        
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }
        
        // Optional: Cannot delete other admins
        // if ($model->isAdmin()) {
        //     return false;
        // }
        
        return true;
    }

    /**
     * Determine if the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }
}
```

#### 7.3 Register Policy
**File:** `app/Providers/AppServiceProvider.php`

```php
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::policy(User::class, UserPolicy::class);
}
```

#### 7.4 Use Policy in Components
```php
// In Livewire component
$this->authorize('delete', $user);

// In Blade
@can('delete', $user)
    <flux:button wire:click="confirmDelete({{ $user->id }})">
        Delete
    </flux:button>
@endcan
```

---

## Step 8: Run Tests and Fix (Green Phase)

### What
Implement code to make all tests pass

### Why
TDD cycle: Red → Green → Refactor

### Process

#### 8.1 Run Tests
```bash
php artisan test --filter=UserManagementTest
```

#### 8.2 Fix Failing Tests
Go through each failing test and implement the required functionality:

1. **Test: admin can soft delete a user**
   - Ensure `delete()` method exists
   - Ensure soft delete is performed
   - Ensure success message is set

2. **Test: admin cannot delete themselves**
   - Add self-deletion check
   - Set error message

3. **Test: soft deleted users do not appear**
   - Verify query excludes trashed (automatic with SoftDeletes)

4. **Test: authorization tests**
   - Add policy checks
   - Return 403 for unauthorized users

#### 8.3 Iterate
- Fix one test at a time
- Run tests after each fix
- Ensure no regressions

#### 8.4 All Green
When all tests pass, move to refactor phase.

---

## Step 9: Refactor (Refactor Phase)

### What
Clean up code, remove duplication, improve readability

### Why
TDD cycle completion: Red → Green → **Refactor**

### Refactoring Checklist

#### 9.1 Remove Duplication
- Extract common validation logic
- Share authorization checks
- Create helper methods

#### 9.2 Improve Names
- Clear method names
- Descriptive variable names
- Consistent naming conventions

#### 9.3 Simplify Logic
- Reduce complexity
- Early returns
- Guard clauses

#### 9.4 Add Comments (Sparingly)
- Document complex logic
- Explain business rules
- Don't state the obvious

#### 9.5 Example Refactoring

**Before:**
```php
public function delete()
{
    if (!$this->userToDelete) {
        return;
    }
    
    $user = User::findOrFail($this->userToDelete);
    $this->authorize('delete', $user);
    
    if ($user->id === auth()->id()) {
        session()->flash('error', 'You cannot delete yourself.');
        $this->cancelDelete();
        return;
    }
    
    $user->delete();
    
    session()->flash('success', "User '{$user->name}' has been deleted successfully.");
    
    $this->cancelDelete();
    $this->resetPage();
}
```

**After:**
```php
public function delete()
{
    $user = $this->getUserToDelete();
    
    if (!$user) {
        return;
    }
    
    if ($this->isSelfDeletion($user)) {
        $this->flashError('You cannot delete yourself.');
        return $this->cancelDelete();
    }
    
    $this->performDeletion($user);
    $this->flashSuccess($user->name);
    $this->cleanupAfterDeletion();
}

private function getUserToDelete(): ?User
{
    if (!$this->userToDelete) {
        return null;
    }
    
    $user = User::findOrFail($this->userToDelete);
    $this->authorize('delete', $user);
    
    return $user;
}

private function isSelfDeletion(User $user): bool
{
    return $user->id === auth()->id();
}

private function performDeletion(User $user): void
{
    $user->delete();
}

private function flashSuccess(string $userName): void
{
    session()->flash('success', "User '{$userName}' has been deleted successfully.");
}

private function flashError(string $message): void
{
    session()->flash('error', $message);
}

private function cleanupAfterDeletion(): void
{
    $this->cancelDelete();
    $this->resetPage();
}
```

#### 9.6 Run Tests Again
After refactoring, ensure all tests still pass:

```bash
php artisan test --filter=UserManagementTest
```

---

## Step 10: Manual Testing

### What
Test the functionality in a real browser

### Why
Automated tests don't catch everything:
- UI/UX issues
- Visual feedback
- User flow
- Edge cases in real scenarios

### Test Scenarios

#### 10.1 Happy Path
1. ✅ Login as admin
2. ✅ Navigate to user management
3. ✅ Find a user to delete
4. ✅ Click delete button
5. ✅ See confirmation dialog
6. ✅ Confirm deletion
7. ✅ See success message
8. ✅ Verify user removed from list
9. ✅ Check database: deleted_at should be set

#### 10.2 Self-Deletion Prevention
1. ✅ Login as admin
2. ✅ Navigate to user management
3. ✅ Try to delete yourself
4. ✅ See error message
5. ✅ Verify you're still in the list

#### 10.3 Authorization
1. ✅ Login as doktor
2. ✅ Try to access user management
3. ✅ Should see 403 or redirect

4. ✅ Login as pacijent
5. ✅ Try to access user management
6. ✅ Should see 403 or redirect

#### 10.4 Cancel Flow
1. ✅ Login as admin
2. ✅ Click delete button
3. ✅ See confirmation dialog
4. ✅ Click cancel
5. ✅ Dialog closes
6. ✅ No deletion occurs

#### 10.5 Search/Filter Interaction
1. ✅ Delete a user
2. ✅ Search for deleted user
3. ✅ Should not appear
4. ✅ Apply role filter
5. ✅ Deleted users should not appear

#### 10.6 Database Verification
```bash
php artisan tinker
```

```php
// Check soft deleted users
User::onlyTrashed()->get();

// Check specific user
$user = User::withTrashed()->find(5);
$user->deleted_at; // Should have timestamp

// Restore a user (if implemented)
$user->restore();
```

### Manual Testing Checklist

- [ ] Delete button appears for each user
- [ ] Delete button has correct styling (danger/red)
- [ ] Confirmation dialog appears
- [ ] Confirmation dialog shows correct user name
- [ ] Cancel button works
- [ ] Delete button in dialog works
- [ ] Loading state shows during deletion
- [ ] Success message appears after deletion
- [ ] User disappears from list
- [ ] Pagination works after deletion
- [ ] Search still works after deletion
- [ ] Filter still works after deletion
- [ ] Cannot delete yourself (error shown)
- [ ] Non-admins cannot access delete functionality
- [ ] Database has deleted_at timestamp set
- [ ] User can be found with User::withTrashed()

---

## Summary

### TDD Cycle Summary

1. **Red Phase**: Write failing tests
2. **Green Phase**: Make tests pass with minimal code
3. **Refactor Phase**: Clean up code while keeping tests green

### Implementation Summary

You have two main options:

**Option A: Dedicated DeleteUser Component**
- More modular
- Easier to test in isolation
- More files to maintain

**Option B: Delete in UserManagement Component**
- Simpler
- All in one place
- Recommended for this use case

### Key Features Implemented

✅ Soft delete functionality  
✅ Self-deletion prevention  
✅ Authorization (only admin can delete)  
✅ Confirmation dialog  
✅ Success/error messages  
✅ Automatic query filtering  
✅ Comprehensive tests  

### Next Steps (Future Enhancements)

- [ ] Restore deleted users functionality
- [ ] View deleted users (trash view)
- [ ] Permanent delete (force delete)
- [ ] Bulk delete
- [ ] Delete confirmation with reason
- [ ] Activity log for deletions
- [ ] Email notification on deletion

---

## Quick Command Reference

```bash
# Create migration
php artisan make:migration add_soft_deletes_to_users_table

# Run migration
php artisan migrate

# Create Livewire component
php artisan make:livewire Admin/DeleteUser

# Create policy
php artisan make:policy UserPolicy --model=User

# Run tests
php artisan test --filter=UserManagementTest

# Run specific test
php artisan test --filter="admin can soft delete a user"

# Run tests with coverage
php artisan test --coverage

# Open tinker
php artisan tinker
```

---

## Troubleshooting

### Tests Still Failing After Implementation

1. **Check authorization**: Ensure policy is registered
2. **Check database**: Migration ran successfully
3. **Check model**: SoftDeletes trait added
4. **Check session**: Flash messages set correctly
5. **Clear cache**: `php artisan optimize:clear`

### UI Not Showing Delete Button

1. Check authorization: `@can('delete', $user)`
2. Check Livewire is loaded: `@livewireScripts`
3. Check JavaScript console for errors
4. Verify Flux UI components are working

### Soft Delete Not Working

1. Verify migration ran: Check users table for deleted_at column
2. Verify model has SoftDeletes trait
3. Check you're using `delete()` not `forceDelete()`
4. Check database directly to confirm

### Authorization Not Working

1. Verify policy is registered in AppServiceProvider
2. Check policy method names match
3. Verify user roles are correct
4. Test policy in tinker: `Gate::allows('delete', $user)`

---

## Resources

- [Laravel Soft Deletes Documentation](https://laravel.com/docs/eloquent#soft-deleting)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [Laravel Authorization Documentation](https://laravel.com/docs/authorization)
- [Pest Testing Documentation](https://pestphp.com/docs)
- [TDD Best Practices](https://laravel.com/docs/testing)

---

*This guide follows TDD principles and Laravel best practices for implementing soft delete functionality in a Livewire application.*
