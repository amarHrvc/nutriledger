<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit User</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update user information</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
        {{-- 
            wire:submit="save" - Calls save() method on submit
            Livewire prevents default form submission automatically
        --}}
        <form wire:submit="save" class="space-y-6">
            
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Name <span class="text-red-500">*</span>
                </label>
                {{-- 
                    wire:model="name" - Two-way binding
                    Real-time validation on blur (updatedName() hook)
                --}}
                <input
                    type="text"
                    id="name"
                    wire:model.blur="name"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    required
                />
                {{-- Show validation error if exists --}}
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Email <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    wire:model.blur="email"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    required
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Field -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Role <span class="text-red-500">*</span>
                </label>
                <select
                    id="role"
                    wire:model="role"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    required
                >
                    <option value="pacijent">Pacijent</option>
                    <option value="doktor">Doktor</option>
                    <option value="admin">Admin</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Section (Optional on Edit) --}}
            <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Change Password (Optional)
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Leave blank to keep current password
                </p>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        New Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        wire:model="password"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Confirm New Password
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        wire:model="password_confirmation"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    />
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4">
                {{-- 
                    wire:navigate - SPA-like navigation (no full page reload)
                --}}
                <a
                    href="{{ route('admin.users.index') }}"
                    wire:navigate
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Cancel
                </a>
                
                {{--
                    wire:loading - Shows during AJAX request
                    wire:target="save" - Only show when save() is running
                --}}
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">Update User</span>
                    <span wire:loading wire:target="save">Updating...</span>
                </button>
            </div>
        </form>
    </div>
</div>
