# UserManagementTest.php - Refactoring to Livewire

## Summary
- **Total Tests**: 36
- **Already Passing (Green)**: 24 ✅
- **Need Refactoring (Red)**: 12 ❌

---

## ✅ ALREADY REFACTORED & PASSING (24 tests)

### Access Control Tests (4 tests) - ✅ NO CHANGES NEEDED
These test HTTP routes, not Livewire components:
1. `admin can access user management index page` ✅
2. `doktor cannot access user management index page` ✅
3. `pacijent cannot access user management index page` ✅
4. `guest cannot access user management index page` ✅

### List Users Tests (3 tests) - ✅ NO CHANGES NEEDED
These test the index page, not mutation operations:
5. `admin can see list of all users` ✅
6. `admin can filter users by role` ✅
7. `admin can search users by name or email` ✅

### Create User Tests (11 tests) - ✅ ALREADY REFACTORED
8. `admin can view create user form` ✅
9. `admin can create a new doctor` ✅ (Uses Livewire::test)
10. `admin can create a new patient` ✅ (Uses Livewire::test)
11. `admin can create a new admin` ✅ (Uses Livewire::test)
12. `create user requires name` ✅ (Uses Livewire::test)
13. `create user requires email` ✅ (Uses Livewire::test)
14. `create user requires valid email` ✅ (Uses Livewire::test)
15. `create user requires unique email` ✅ (Uses Livewire::test)
16. `create user requires password` ✅ (Uses Livewire::test)
17. `create user requires password confirmation` ✅ (Uses Livewire::test)
18. `create user requires minimum password length` ✅ (Uses Livewire::test)
19. `create user requires valid role` ✅ (Uses Livewire::test)

### Update User Tests (5 tests) - ✅ ALREADY REFACTORED
20. `admin can view edit user form` ✅
21. `admin can update user details` ✅ (Uses Livewire::test)
22. `admin can change user role` ✅ (Uses Livewire::test)
23. `admin can update user password` ✅ (Uses Livewire::test)

### Confirmation Test (1 test) - ✅ NO CHANGES NEEDED
24. `deleting user shows confirmation` ✅

---

## ❌ NEED REFACTORING TO LIVEWIRE (12 tests)

### Update User Validation Tests (4 tests) - Lines 315-381
**Issue**: Using `->put()` HTTP requests, but EditUser is a Livewire component
**Component**: `App\Livewire\Admin\EditUser`

#### ❌ Test #1: `update user password is optional` (Line 315)
**Current**: Uses PUT request `/admin/users/{id}`
**Need**: 
```php
Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
    ->set('name', 'Updated Name')
    ->call('save')
    ->assertHasNoErrors()
    ->assertRedirect(route('admin.users.index'));
// Then verify password unchanged
```

#### ❌ Test #2: `update user requires password confirmation when password provided` (Line 335)
**Current**: Uses PUT request
**Need**:
```php
Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
    ->set('password', 'newpassword123')
    ->call('save')
    ->assertHasErrors('password'); // Should fail because no confirmation
```

#### ❌ Test #3: `update user email must be unique` (Line 351)
**Current**: Uses PUT request
**Need**:
```php
Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user2])
    ->set('email', 'user1@example.com') // Already exists
    ->call('save')
    ->assertHasErrors('email');
```

#### ❌ Test #4: `update user can keep same email` (Line 367)
**Current**: Uses PUT request
**Need**:
```php
Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
    ->set('name', 'Updated Name')
    ->set('email', 'same@example.com') // Keep same email
    ->call('save')
    ->assertHasNoErrors()
    ->assertRedirect(route('admin.users.index'));
```

---

### Delete User Tests (2 tests) - Lines 384-407
**Issue**: Using `->delete()` HTTP requests, but no DELETE route exists
**Action Needed**: Either create a Livewire delete component OR implement delete in UserManagement component

#### ❌ Test #5: `admin can delete a user` (Line 384)
**Current**: Uses DELETE request `/admin/users/{id}`
**Options**:
```php
// Option A: If delete is in UserManagement component
Livewire::test(\App\Livewire\Admin\UserManagement::class)
    ->call('deleteUser', $user->id)
    ->assertDispatched('user-deleted'); // Or check for session success

// Option B: Create separate DeleteUser component
Livewire::test(\App\Livewire\Admin\DeleteUser::class, ['user' => $user])
    ->call('delete')
    ->assertRedirect(route('admin.users.index'))
    ->assertSessionHas('success');
```

#### ❌ Test #6: `admin cannot delete themselves` (Line 398)
**Current**: Uses DELETE request
**Need**: Similar to above, test self-deletion prevention:
```php
Livewire::actingAs($admin)
    ->test(\App\Livewire\Admin\UserManagement::class)
    ->call('deleteUser', $admin->id)
    ->assertHasErrors(); // Or assertForbidden()
```

---

### Authorization Tests (6 tests) - Lines 420-484
**Issue**: Testing non-existent HTTP routes (POST/PUT/DELETE)
**Action Needed**: Test Livewire component authorization using `assertForbidden()` or `assertUnauthorized()`

#### ❌ Test #7: `doktor cannot create users` (Line 420)
**Current**: POST to `/admin/users` returns 405 (Method Not Allowed)
**Need**:
```php
Livewire::actingAs($this->doktor)
    ->test(\App\Livewire\Admin\CreateUser::class)
    ->set('name', 'New User')
    ->set('email', 'new@example.com')
    ->set('password', 'password123')
    ->set('password_confirmation', 'password123')
    ->set('role', 'pacijent')
    ->call('save')
    ->assertForbidden(); // Or assertUnauthorized()
```
**Note**: Requires adding authorization check in CreateUser component

#### ❌ Test #8: `doktor cannot update users` (Line 435)
**Current**: PUT returns 404
**Need**:
```php
$user = User::factory()->create();
Livewire::actingAs($this->doktor)
    ->test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
    ->set('name', 'Updated')
    ->call('save')
    ->assertForbidden();
```

#### ❌ Test #9: `doktor cannot delete users` (Line 444)
**Current**: DELETE returns 404
**Need**:
```php
$user = User::factory()->create();
Livewire::actingAs($this->doktor)
    ->test(\App\Livewire\Admin\UserManagement::class)
    ->call('deleteUser', $user->id)
    ->assertForbidden();
```

#### ❌ Test #10: `pacijent cannot create users` (Line 453)
**Same as Test #7, but with `$this->pacijent`**

#### ❌ Test #11: `pacijent cannot update users` (Line 468)
**Same as Test #8, but with `$this->pacijent`**

#### ❌ Test #12: `pacijent cannot delete users` (Line 477)
**Same as Test #9, but with `$this->pacijent`**

---

## IMPLEMENTATION CHECKLIST

### Before Refactoring Tests:
- [ ] **Check if delete functionality exists** in any Livewire component
- [ ] **Add authorization checks** to CreateUser and EditUser components
  ```php
  public function save()
  {
      if (auth()->user()->role !== 'admin') {
          abort(403);
      }
      // ... rest of save logic
  }
  ```
- [ ] **Decide on delete implementation**: 
  - Option A: Add `deleteUser()` method to UserManagement component
  - Option B: Create new `DeleteUser` Livewire component

### After Implementation:
- [ ] Refactor Tests #1-4 (Update validation tests)
- [ ] Refactor Tests #5-6 (Delete tests)
- [ ] Refactor Tests #7-12 (Authorization tests)
- [ ] Run full test suite to verify all 36 tests pass

---

## LARAVEL BOOST TOOLS USED

1. **laravel-boost-application-info** - Verified Livewire 3.6.4 is installed
2. **laravel-boost-list-routes** - Confirmed no POST/PUT/DELETE routes exist
3. **laravel-boost-search-docs** - Found Livewire testing documentation:
   - `assertForbidden()` for authorization
   - `assertUnauthorized()` for authentication
   - `Livewire::actingAs($user)` for testing as different users
   - `->call('method', param)` for calling component methods

---

## TESTING PATTERN SUMMARY

### ✅ Correct Livewire Testing Pattern:
```php
Livewire::actingAs($user)                              // Set authenticated user
    ->test(ComponentClass::class, ['param' => $value]) // Mount component
    ->set('property', 'value')                         // Set properties
    ->call('method')                                   // Call method
    ->assertHasNoErrors()                              // Check validation (do this FIRST!)
    ->assertRedirect(route('name'))                    // Then check redirect
    ->assertSessionHas('success');                     // Check session flash
```

### ❌ Wrong Pattern (used in failing tests):
```php
$this->actingAs($user)
    ->put('/admin/users/{id}', $data)   // Routes don't exist!
    ->assertRedirect('/admin/users');
```

---

## PRIORITY ORDER

1. **HIGH**: Tests #1-4 (Update validation) - Easy fix, just change to Livewire testing
2. **MEDIUM**: Tests #5-6 (Delete) - Requires implementing delete functionality first
3. **LOW**: Tests #7-12 (Authorization) - Requires adding authorization to components first
