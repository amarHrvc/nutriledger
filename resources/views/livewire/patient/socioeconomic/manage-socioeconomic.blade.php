<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ $patient->fullName }}</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">Socioeconomic Data</flux:text>
        </div>
        <flux:button
            :href="$isEditing ? route('patients.socioeconomic.show', $patient) : route('patients.show', $patient)"
            variant="ghost"
            icon="arrow-left"
        >
            Back
        </flux:button>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    <form wire:submit="save" class="space-y-6">

        {{-- 1. Demographics --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
            <flux:heading size="lg">Demographics</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Marital Status</flux:label>
                    <flux:select wire:model="marital_status">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="single">Single</flux:select.option>
                        <flux:select.option value="married">Married</flux:select.option>
                        <flux:select.option value="divorced">Divorced</flux:select.option>
                        <flux:select.option value="widowed">Widowed</flux:select.option>
                        <flux:select.option value="separated">Separated</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    @error('marital_status') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Number of Dependents</flux:label>
                    <flux:input type="number" wire:model="number_of_dependents" min="0" max="20" />
                    @error('number_of_dependents') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Living Arrangement</flux:label>
                    <flux:select wire:model="living_arrangement">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="alone">Alone</flux:select.option>
                        <flux:select.option value="with_family">With Family</flux:select.option>
                        <flux:select.option value="with_partner">With Partner</flux:select.option>
                        <flux:select.option value="shared_housing">Shared Housing</flux:select.option>
                        <flux:select.option value="care_facility">Care Facility</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    @error('living_arrangement') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>
        </div>

        {{-- 2. Economic Status --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
            <flux:heading size="lg">Economic Status</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Employment Status</flux:label>
                    <flux:select wire:model="employment_status">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="employed_full_time">Employed Full Time</flux:select.option>
                        <flux:select.option value="employed_part_time">Employed Part Time</flux:select.option>
                        <flux:select.option value="self_employed">Self Employed</flux:select.option>
                        <flux:select.option value="unemployed">Unemployed</flux:select.option>
                        <flux:select.option value="retired">Retired</flux:select.option>
                        <flux:select.option value="student">Student</flux:select.option>
                        <flux:select.option value="unable_to_work">Unable to Work</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    @error('employment_status') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Occupation</flux:label>
                    <flux:input type="text" wire:model="occupation" />
                    @error('occupation') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Income Level</flux:label>
                    <flux:select wire:model="income_level">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="low">Low</flux:select.option>
                        <flux:select.option value="lower_middle">Lower Middle</flux:select.option>
                        <flux:select.option value="middle">Middle</flux:select.option>
                        <flux:select.option value="upper_middle">Upper Middle</flux:select.option>
                        <flux:select.option value="high">High</flux:select.option>
                    </flux:select>
                    @error('income_level') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:checkbox wire:model="has_health_insurance" label="Has Health Insurance" />
                    @error('has_health_insurance') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>
        </div>

        {{-- 3. Lifestyle --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
            <flux:heading size="lg">Lifestyle</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Education Level</flux:label>
                    <flux:select wire:model="education_level">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="no_formal">No Formal Education</flux:select.option>
                        <flux:select.option value="primary">Primary</flux:select.option>
                        <flux:select.option value="secondary">Secondary</flux:select.option>
                        <flux:select.option value="vocational">Vocational</flux:select.option>
                        <flux:select.option value="bachelors">Bachelor's</flux:select.option>
                        <flux:select.option value="masters">Master's</flux:select.option>
                        <flux:select.option value="doctorate">Doctorate</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    @error('education_level') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Smoking Status</flux:label>
                    <flux:select wire:model="smoking_status">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="never">Never</flux:select.option>
                        <flux:select.option value="former">Former</flux:select.option>
                        <flux:select.option value="current_light">Current Light</flux:select.option>
                        <flux:select.option value="current_heavy">Current Heavy</flux:select.option>
                    </flux:select>
                    @error('smoking_status') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Alcohol Consumption</flux:label>
                    <flux:select wire:model="alcohol_consumption">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="none">None</flux:select.option>
                        <flux:select.option value="occasional">Occasional</flux:select.option>
                        <flux:select.option value="moderate">Moderate</flux:select.option>
                        <flux:select.option value="heavy">Heavy</flux:select.option>
                    </flux:select>
                    @error('alcohol_consumption') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Physical Activity Level</flux:label>
                    <flux:select wire:model="physical_activity_level">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="sedentary">Sedentary</flux:select.option>
                        <flux:select.option value="lightly_active">Lightly Active</flux:select.option>
                        <flux:select.option value="moderately_active">Moderately Active</flux:select.option>
                        <flux:select.option value="very_active">Very Active</flux:select.option>
                    </flux:select>
                    @error('physical_activity_level') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>
        </div>

        {{-- 4. Support System --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
            <flux:heading size="lg">Support System</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:checkbox wire:model="has_family_support" label="Has Family Support" />
                    @error('has_family_support') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:checkbox wire:model="has_caregiver" label="Has Caregiver" />
                    @error('has_caregiver') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Transportation Access</flux:label>
                    <flux:select wire:model="transportation_access">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="own_vehicle">Own Vehicle</flux:select.option>
                        <flux:select.option value="public_transport">Public Transport</flux:select.option>
                        <flux:select.option value="rideshare">Rideshare</flux:select.option>
                        <flux:select.option value="walking">Walking</flux:select.option>
                        <flux:select.option value="limited">Limited</flux:select.option>
                        <flux:select.option value="none">None</flux:select.option>
                    </flux:select>
                    @error('transportation_access') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>
        </div>

        {{-- 5. Food Security --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
            <flux:heading size="lg">Food Security</flux:heading>
            <flux:separator />

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Food Security Status</flux:label>
                    <flux:select wire:model="food_security_status">
                        <flux:select.option value="">— Select —</flux:select.option>
                        <flux:select.option value="food_secure">Food Secure</flux:select.option>
                        <flux:select.option value="marginally_secure">Marginally Secure</flux:select.option>
                        <flux:select.option value="food_insecure">Food Insecure</flux:select.option>
                        <flux:select.option value="severely_insecure">Severely Insecure</flux:select.option>
                    </flux:select>
                    @error('food_security_status') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Dietary Restrictions / Cultural Considerations</flux:label>
                    <flux:textarea wire:model="dietary_restrictions_cultural" rows="3" />
                    @error('dietary_restrictions_cultural') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>
        </div>

        {{-- 6. Additional Notes --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">
            <flux:heading size="lg">Additional Notes</flux:heading>
            <flux:separator />

            <flux:field>
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="additional_notes" rows="4" />
                @error('additional_notes') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end">
            <flux:button type="submit" variant="primary" class="cursor-pointer">
                {{ $isEditing ? 'Update' : 'Save' }}
            </flux:button>
        </div>

    </form>
</div>
