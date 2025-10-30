<x-layouts.app :title="__('Edit User')">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        {{-- 
            Livewire component with route model binding
            Laravel automatically passes {user} from URL to component
        --}}
        <livewire:admin.edit-user :user="$user" />
    </div>
</x-layouts.app>
