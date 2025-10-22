<x-layouts.app :title="__('Edit User')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <livewire:admin.edit-user :user="$user" />
    </div>
</x-layouts.app>
