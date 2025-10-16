<div>
    <div class="max-w-4xl mx-auto py-6 px-4">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <flux:heading size="xl">{{ $patient->full_name }}</flux:heading>
                <flux:text class="text-zinc-500">Socioeconomic Profile</flux:text>
            </div>
            <div class="flex gap-3">
                @if($socioeconomic)
                    @can('update', $socioeconomic)
                        <flux:button href="{{ route('patients.socioeconomic.edit', $patient) }}" wire:navigate variant="primary">
                            Edit
                        </flux:button>
                    @endcan
                @endif
                <flux:button href="{{ route('patients.show', $patient) }}" wire:navigate variant="ghost">
                    Back to Patient Profile
                </flux:button>
            </div>
        </div>

        @if(!$socioeconomic)
            {{-- Empty state --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:text class="text-zinc-500 mb-4">No socioeconomic data recorded yet.</flux:text>
                @can('create', App\Models\PatientSocioeconomic::class)
                    <flux:button href="{{ route('patients.socioeconomic.create', $patient) }}" wire:navigate variant="primary">
                        Add Socioeconomic Data
                    </flux:button>
                @endcan
            </div>
        @else
            <div class="space-y-6">
                {{-- Demographics --}}
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Demographics</flux:heading>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Marital Status</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->marital_status) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Number of Dependents</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $socioeconomic->number_of_dependents ?? 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Living Arrangement</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->living_arrangement) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Economic Status --}}
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Economic Status</flux:heading>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Employment Status</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->employment_status) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Occupation</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $socioeconomic->occupation ?? 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Income Level</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->income_level) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Health Insurance</dt>
                            <dd class="mt-1">
                                @if($socioeconomic->has_health_insurance)
                                    <flux:badge color="green">Yes</flux:badge>
                                @else
                                    <flux:badge color="red">No</flux:badge>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Lifestyle --}}
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Lifestyle</flux:heading>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Education Level</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->education_level) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Smoking Status</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->smoking_status) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Alcohol Consumption</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->alcohol_consumption) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Physical Activity Level</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->physical_activity_level) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Support System --}}
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Support System</flux:heading>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Family Support</dt>
                            <dd class="mt-1">
                                @if($socioeconomic->has_family_support)
                                    <flux:badge color="green">Yes</flux:badge>
                                @else
                                    <flux:badge color="red">No</flux:badge>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Has Caregiver</dt>
                            <dd class="mt-1">
                                @if($socioeconomic->has_caregiver)
                                    <flux:badge color="green">Yes</flux:badge>
                                @else
                                    <flux:badge color="red">No</flux:badge>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Transportation Access</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->transportation_access) }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Food Security --}}
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Food Security</flux:heading>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Food Security Status</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ format_enum_label($socioeconomic->food_security_status) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500">Dietary Restrictions / Cultural</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $socioeconomic->dietary_restrictions_cultural ?? 'None' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Additional Notes --}}
                @if($socioeconomic->additional_notes)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <flux:heading size="lg" class="mb-4">Additional Notes</flux:heading>
                        <flux:text class="text-zinc-900 dark:text-zinc-100">{{ $socioeconomic->additional_notes }}</flux:text>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
