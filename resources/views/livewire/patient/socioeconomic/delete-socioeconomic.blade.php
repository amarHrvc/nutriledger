<div>
    <div class="max-w-xl mx-auto py-8 px-4">
        <div class="mb-6">
            <flux:button href="{{ route('patients.socioeconomic.show', $patient) }}" wire:navigate variant="ghost" class="cursor-pointer">
                &larr; Back to Socioeconomic Data
            </flux:button>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
            <flux:heading size="xl" class="mb-4">Delete Socioeconomic Data</flux:heading>

            <p class="text-zinc-600 dark:text-zinc-400 mb-6">
                You are about to permanently delete the socioeconomic data for
                <strong>{{ $patient->full_name }}</strong>.
                This action cannot be undone.
            </p>

            <div class="flex gap-3">
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">
                    Confirm Delete
                </flux:button>

                <flux:button href="{{ route('patients.socioeconomic.show', $patient) }}" wire:navigate variant="ghost" class="cursor-pointer">
                    Cancel
                </flux:button>
            </div>
        </div>
    </div>
</div>
