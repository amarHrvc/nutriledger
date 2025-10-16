<!-- file: `resources/views/livewire/patient/visit/create-visit.blade.php` -->
<div class="p-6" wire:poll.remove>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="lg">
                {{ 'Create Visit — ' . ($patient->first_name ? $patient->first_name . ' ' . $patient->last_name : ($patient->name ?? 'Patient')) }}
            </flux:heading>
            <p class="text-sm text-muted mt-1">Log a new visit for this patient.</p>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <flux:field label="Date" for="date">
                <flux:input id="date" type="date" wire:model.live="form.date"  />
                @error('form.date') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </flux:field>
        </div>

        <div>
            <flux:field label="Notes" for="notes">
                <flux:textarea id="notes" wire:model.live="form.notes" rows="6" />
                @error('form.notes') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
            </flux:field>
        </div>

        @if (! empty($doctors))
            <div>
                <flux:field label="Doctor" for="doctor_id">
                    <flux:select id="doctor_id" wire:model.live="form.doctor_id">
                        <option value="">Select doctor</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                        @endforeach
                    </flux:select>
                    @error('form.doctor_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </flux:field>
            </div>
        @else
            <div class="text-sm text-muted">Doctor will be set to the current user.</div>
        @endif

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Save Visit</span>
                <span wire:loading>Saving…</span>
            </flux:button>

            <a href="{{ route('patients.show', $patient) }}" class="text-sm text-muted hover:underline">Cancel</a>
        </div>
    </form>
</div>
