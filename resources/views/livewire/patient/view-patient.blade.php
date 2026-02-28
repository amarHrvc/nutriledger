<div>
    <div class="max-w-4xl mx-auto py-6 px-4">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <flux:heading size="xl">{{ $patient->full_name }}</flux:heading>
                <flux:text class="text-zinc-500">Patient Profile</flux:text>
            </div>
            <div class="flex gap-3">
                @can('update', $patient)
                    <flux:button href="{{ route('patients.edit', $patient) }}" wire:navigate variant="primary">
                        Edit Profile
                    </flux:button>
                @endcan
                <flux:button href="{{ route('patients.index') }}" wire:navigate variant="ghost">
                    Back to List
                </flux:button>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Personal Information --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Personal Information</flux:heading>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-zinc-500">Full Name</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500">Gender</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->gender === 'M' ? 'Male' : 'Female' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500">Date of Birth</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->date_of_birth?->format('d/m/Y') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500">Age</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->age ?? 'N/A' }} years</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500">Phone</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->phone ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500">Blood Type</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->blood_type ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Address Information --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Address Information</flux:heading>
                @if($patient->address || $patient->city || $patient->postal_code)
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-zinc-500">Street Address</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->address ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">City</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->city ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Postal Code</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->postal_code ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                @else
                    <flux:text class="text-zinc-500">No address provided</flux:text>
                @endif
            </div>

            {{-- Emergency Contact --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Emergency Contact</flux:heading>
                @if($patient->emergency_contact_name || $patient->emergency_contact_phone)
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Contact Name</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->emergency_contact_name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Contact Phone</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->emergency_contact_phone ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                @else
                    <flux:text class="text-zinc-500">No emergency contact provided</flux:text>
                @endif
            </div>

            {{-- Medical Information --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Medical Information</flux:heading>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-zinc-500">Allergies</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->allergies ?? 'None reported' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-zinc-500">Medical Notes</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $patient->medical_notes ?? 'No notes' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
