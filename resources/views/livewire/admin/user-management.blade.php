<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">User Management</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage all users in the system</p>
    </div>

    <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex-1">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Search by name or email..."
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            />
        </div>

        <div class="flex gap-2">
            <select
                wire:model.live="roleFilter"
                class="rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            >
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="doktor">Doktor</option>
                <option value="pacijent">Pacijent</option>
            </select>

            <a
                href="{{ route('admin.users.create') }}"
                wire:navigate
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Create User !!!!!!!!!!!
            </a>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @forelse ($users as $user)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $user->name }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                @if($user->role === 'admin') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @elseif($user->role === 'doktor') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a
                                    href="{{ route('admin.users.edit', $user) }}"
                                    wire:navigate
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    Edit
                                </a>

                                @can('delete', $user)
                                    <flux:modal.trigger :name="'delete-user-' . $user->id">
                                        <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            Delete
                                        </button>
                                    </flux:modal.trigger>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    {{-- Delete Confirmation Modals --}}
    @foreach ($users as $user)
        @can('delete', $user)
            <flux:modal :name="'delete-user-' . $user->id" class="min-w-[22rem]">
                <form wire:submit="deleteUser({{ $user->id }})">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Delete User?</flux:heading>
                            <flux:text class="mt-2">
                                You're about to delete <strong>{{ $user->name }}</strong> ({{ $user->email }}).<br>
                                This action cannot be reversed.
                            </flux:text>
                        </div>
                        <div class="flex gap-2">
                            <flux:spacer />
                            <flux:modal.close>
                                <flux:button variant="ghost">Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" variant="danger">Delete User</flux:button>
                        </div>
                    </div>
                </form>
            </flux:modal>
        @endcan
    @endforeach
</div>
