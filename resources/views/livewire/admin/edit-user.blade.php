<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit User</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update user information</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-900">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    required
                />
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Email <span class="text-red-500">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    required
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Role <span class="text-red-500">*</span>
                </label>
                <select
                    id="role"
                    name="role"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    required
                >
                    <option value="pacijent" {{ old('role', $user->role) == 'pacijent' ? 'selected' : '' }}>Pacijent</option>
                    <option value="doktor" {{ old('role', $user->role) == 'doktor' ? 'selected' : '' }}>Doktor</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (Optional) -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Password <span class="text-sm text-gray-500">(Leave blank to keep current password)</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Confirm Password
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
