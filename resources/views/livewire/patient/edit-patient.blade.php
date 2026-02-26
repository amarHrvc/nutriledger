<div>
    <div class="max-w-4xl mx-auto py-6 px-4">
        <div class="mb-6">
            <flux:heading size="xl">Edit Patient</flux:heading>
            <flux:text class="text-zinc-500">Update patient profile for {{ $patient->full_name }}</flux:text>
        </div>

        <form wire:submit="updatePatient" class="space-y-6">
            {{-- Account Information (read-only) --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Account Information</flux:heading>
                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input type="email" value="{{ $patient->user?->email }}" disabled />
                    <flux:description>Email cannot be changed here</flux:description>
                </flux:field>
            </div>

            {{-- Personal Information --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Personal Information</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>First Name *</flux:label>
                        <flux:input wire:model="first_name" />
                        <flux:error name="first_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Last Name *</flux:label>
                        <flux:input wire:model="last_name" />
                        <flux:error name="last_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Date of Birth *</flux:label>
                        <flux:input type="date" wire:model="date_of_birth" />
                        <flux:error name="date_of_birth" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Gender *</flux:label>
                        <flux:select wire:model="gender">
                            <flux:select.option value="">Select Gender</flux:select.option>
                            <flux:select.option value="M">Male</flux:select.option>
                            <flux:select.option value="F">Female</flux:select.option>
                        </flux:select>
                        <flux:error name="gender" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Phone</flux:label>
                        <flux:input wire:model="phone" />
                        <flux:error name="phone" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Blood Type</flux:label>
                        <flux:select wire:model="blood_type">
                            <flux:select.option value="">Select Blood Type</flux:select.option>
                            <flux:select.option value="A+">A+</flux:select.option>
                            <flux:select.option value="A-">A-</flux:select.option>
                            <flux:select.option value="B+">B+</flux:select.option>
                            <flux:select.option value="B-">B-</flux:select.option>
                            <flux:select.option value="AB+">AB+</flux:select.option>
                            <flux:select.option value="AB-">AB-</flux:select.option>
                            <flux:select.option value="O+">O+</flux:select.option>
                            <flux:select.option value="O-">O-</flux:select.option>
                        </flux:select>
                        <flux:error name="blood_type" />
                    </flux:field>
                </div>
            </div>

            {{-- Address --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Address Information</flux:heading>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Street Address</flux:label>
                        <flux:input wire:model="address" />
                        <flux:error name="address" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>City</flux:label>
                            <flux:input wire:model="city" />
                            <flux:error name="city" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Postal Code</flux:label>
                            <flux:input wire:model="postal_code" />
                            <flux:error name="postal_code" />
                        </flux:field>
                    </div>
                </div>
            </div>

            {{-- Emergency Contact --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Emergency Contact</flux:heading>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Contact Name</flux:label>
                        <flux:input wire:model="emergency_contact_name" />
                        <flux:error name="emergency_contact_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Contact Phone</flux:label>
                        <flux:input wire:model="emergency_contact_phone" />
                        <flux:error name="emergency_contact_phone" />
                    </flux:field>
                </div>
            </div>

            {{-- Medical Information --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="lg" class="mb-4">Medical Information</flux:heading>
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Allergies</flux:label>
                        <flux:textarea wire:model="allergies" rows="2" />
                        <flux:error name="allergies" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Medical Notes</flux:label>
                        <flux:textarea wire:model="medical_notes" rows="3" />
                        <flux:error name="medical_notes" />
                    </flux:field>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('patients.show', $patient) }}" wire:navigate variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" class="cursor-pointer">
                    Update Patient
                </flux:button>
            </div>
        </form>
    </div>
</div>
