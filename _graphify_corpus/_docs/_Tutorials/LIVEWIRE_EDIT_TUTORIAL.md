# Livewire Edit User - Complete Tutorial

## 🎯 What You Built

You now have a complete **Edit User** feature using Livewire! Here's what happens:

1. User clicks "Edit" on user management table
2. Navigates to `/admin/users/{id}/edit` (SPA-like, no full reload)
3. Form pre-populated with user data
4. Real-time validation as they type
5. Updates database on submit
6. Redirects back with success message

---

## 📚 Key Livewire Concepts Explained

### 1. **Component Lifecycle Hooks**

```php
public function mount(User $user)
{
    // Called ONCE when component initializes
    // Perfect for loading initial data
    $this->user = $user;
    $this->name = $user->name;
}
```

**Common Lifecycle Hooks:**
- `mount()` - Initialize component (like __construct)
- `hydrate()` - After component rehydrates from request
- `updating{Property}()` - Before property updates
- `updated{Property}()` - After property updates
- `render()` - Called on every request

### 2. **Route Model Binding with Livewire**

**Route Definition:**
```php
Route::get('/users/{user}/edit', function () {
    return view('admin.users.edit');
})->name('users.edit');
```

**Blade View:**
```blade
<livewire:admin.edit-user :user="$user" />
```

**Component:**
```php
public function mount(User $user)
{
    // $user is automatically loaded by Laravel!
}
```

**Flow:**
1. Laravel sees `{user}` in route
2. Finds User model by ID
3. Passes to view as `$user`
4. View passes to Livewire component
5. Component receives in `mount()`

### 3. **Dynamic Validation Rules**

**Why use a method instead of property?**
```php
// ❌ Property - Can't use dynamic values
protected $rules = [
    'email' => 'unique:users', // Won't exclude current user!
];

// ✅ Method - Can access $this->user
protected function rules()
{
    return [
        'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
    ];
}
```

**Rule::ignore()** ensures the current user's email doesn't fail uniqueness check.

### 4. **Real-Time Validation**

```php
public function updatedEmail()
{
    $this->validateOnly('email');
}
```

Triggered by `wire:model.blur="email"` in blade:
- User types email
- Focuses away (blur event)
- Validation runs immediately
- Errors show without form submit

### 5. **Conditional Data Updates**

```php
public function save()
{
    $updateData = [
        'name' => $validated['name'],
        'email' => $validated['email'],
    ];

    // Only update password if provided
    if (!empty($validated['password'])) {
        $updateData['password'] = Hash::make($validated['password']);
    }

    $this->user->update($updateData);
}
```

**Why?** On edit, password is optional - only update if user enters new one.

---

## 🔄 Wire Modifiers Explained

### `wire:model` Variants

```blade
<!-- Updates on 'change' event (default) -->
<input wire:model="name">

<!-- Updates instantly as you type -->
<input wire:model.live="search">

<!-- Updates after 500ms of no typing -->
<input wire:model.live.debounce.500ms="search">

<!-- Updates when input loses focus -->
<input wire:model.blur="email">

<!-- Updates when Enter is pressed -->
<input wire:model.live.debounce.500ms="search">
```

**Use Cases:**
- `.live` - Filters, instant search
- `.blur` - Email validation, username checks
- `.debounce` - Search boxes, autocomplete
- Default - Most form fields

### `wire:loading` - Loading States

```blade
<button type="submit" wire:loading.attr="disabled" wire:target="save">
    <span wire:loading.remove wire:target="save">Update User</span>
    <span wire:loading wire:target="save">Updating...</span>
</button>
```

**Breakdown:**
- `wire:loading.attr="disabled"` - Disables button during save
- `wire:target="save"` - Only when save() method runs
- `wire:loading.remove` - Hide when loading
- `wire:loading` - Show when loading

---

## 🆚 Create vs Edit - Key Differences

| Aspect | Create | Edit |
|--------|--------|------|
| **mount()** | No parameters | Receives User model |
| **Initial data** | Empty/defaults | Populated from DB |
| **Validation** | `unique:users` | `unique:users,ignore(id)` |
| **Password** | Required | Optional (nullable) |
| **Database** | `User::create()` | `$user->update()` |
| **Route** | `/users/create` | `/users/{user}/edit` |

---

## 🎨 Livewire Features Used

### 1. **Two-Way Data Binding**
```blade
<input wire:model="name">
```
- View ↔ Component property sync
- No manual JavaScript needed

### 2. **Form Submission**
```blade
<form wire:submit="save">
```
- Prevents default submit
- Calls `save()` method
- Validates automatically

### 3. **Error Handling**
```blade
@error('email')
    <p>{{ $message }}</p>
@enderror
```
- Automatic error display
- Per-field validation errors

### 4. **SPA Navigation**
```blade
<a href="..." wire:navigate>
```
- No full page reload
- Faster navigation
- Better UX

### 5. **Flash Messages**
```php
session()->flash('success', 'User updated!');
```
Shows on next page after redirect.

---

## 🔍 How Data Flows

### **Edit User Flow:**

```
1. USER CLICKS EDIT
   ↓
2. ROUTE: /admin/users/5/edit
   ↓
3. LARAVEL ROUTE MODEL BINDING
   - Loads User with ID 5
   ↓
4. BLADE VIEW
   - Receives $user
   - Passes to Livewire component
   ↓
5. LIVEWIRE mount()
   - Receives $user
   - Populates form fields
   ↓
6. BLADE RENDERS
   - wire:model binds inputs
   - Form shows with data
   ↓
7. USER CHANGES FIELD
   - wire:model detects change
   - Sends AJAX to server
   - Updates component property
   ↓
8. REAL-TIME VALIDATION (if using .blur)
   - updatedEmail() runs
   - Validates field
   - Shows errors
   ↓
9. USER SUBMITS FORM
   - wire:submit="save" triggers
   - Validates all fields
   - Updates database
   - Flash message
   - Redirects
```

---

## 🚀 Testing Your Implementation

1. **Navigate to User Management:**
   ```
   http://your-app.test/admin/users
   ```

2. **Click "Edit" on any user**

3. **Test Scenarios:**
   - ✅ Change name → Should save
   - ✅ Change email → Should validate uniqueness
   - ✅ Change role → Should update
   - ✅ Leave password blank → Should NOT change password
   - ✅ Enter new password → Should hash and update
   - ✅ Invalid email → Should show error
   - ✅ Click Cancel → Should navigate back

---

## 🎓 Learning Progression

**You've learned:**
1. ✅ Basic Livewire components (Create User)
2. ✅ Route model binding with Livewire
3. ✅ Component lifecycle (mount hook)
4. ✅ Dynamic validation rules
5. ✅ Real-time validation
6. ✅ Conditional updates
7. ✅ Wire modifiers (.blur, .live)
8. ✅ Loading states
9. ✅ SPA navigation

**Next steps to learn:**
- [ ] Delete with confirmation (wire:confirm)
- [ ] Modal components (edit in modal)
- [ ] File uploads (wire:model for files)
- [ ] Pagination with Livewire
- [ ] Events (component communication)
- [ ] Bulk actions (checkboxes + actions)

---

## 💡 Pro Tips

1. **Always use `validateOnly()` for real-time validation** to avoid validating all fields
2. **Use `.blur` for expensive validations** (database checks)
3. **Use `.debounce` for search** to reduce server requests
4. **Always hash passwords** before storing
5. **Use Rule::ignore()** for unique validation on edit
6. **Flash messages** work great with redirects
7. **wire:navigate** makes navigation feel instant

---

## 🐛 Common Mistakes to Avoid

❌ **Forgetting to ignore current user in unique validation**
```php
'email' => 'unique:users' // Will fail for current user!
```

❌ **Making password required on edit**
```php
'password' => 'required' // User must change password every edit!
```

❌ **Not hashing password**
```php
'password' => $validated['password'] // Stored as plain text!
```

❌ **Using property instead of method for dynamic rules**
```php
protected $rules = [...] // Can't access $this->user->id
```

---

## 📖 Further Reading

- [Livewire Docs - Properties](https://livewire.laravel.com/docs/properties)
- [Livewire Docs - Actions](https://livewire.laravel.com/docs/actions)
- [Livewire Docs - Forms](https://livewire.laravel.com/docs/forms)
- [Livewire Docs - Validation](https://livewire.laravel.com/docs/validation)
- [Laravel Validation Rules](https://laravel.com/docs/validation)

---

**Happy Livewiring! 🎉**
