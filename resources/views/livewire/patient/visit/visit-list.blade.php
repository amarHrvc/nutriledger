<div class="p-6">
    <div class="flux item-center justify-between mb-6">


        <div>
            <flux:heading size="lg">
                {{ 'Visits - ' . $patient->first_name . ' ' . $patient->last_name }}
            </flux:heading>
            <p class="text-sm text-muted mt-1">
                Showing visits for  the selected patient.
            </p>
        </div>


        @can('create', \App\Models\Visit::class)
{{--            <flux:button--}}
{{--                as="a"--}}
{{--                href="{{ route('visit.create', $patient) }}"--}}
{{--                variant="primary">--}}
{{--                    Add Visit--}}
{{--            </flux:button>--}}
        @endcan

        @if($visits->isEmpty())
            <flux:callout type="info" class="mb-4">
                <div>
                    <strong> No visits recorded yet.</strong>
                    <p class="mt-1 text-sm text-muted">
                        You can add visit using the button above.
                    </p>
                </div>
            </flux:callout>
        @else
            <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                <table class="min-w-full divide-y-divide-gray-200">
                    <thead class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($visits as $visit)
                        <tr wire:key="visit-{{$visit->id}}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{optional($visit->date)->format('M d, Y')}} ?? '-'}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{$visit->doctor->first_name ?? '-'}} {{$visit->doctor->last_name ?? ''}}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ \Illuminate\Support\Str::limit($visit->notes ?: '—', 80) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('visits.show', [$patient, $visit]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>

                                @can('update', $visit)
                                    <a href="{{ route('visits.edit', [$patient, $visit]) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        @endif

        <div class="mt-6">
            <a href="{{ route('patients.show', $patient) }}" class="text-sm text-muted hover:underline">Back to Patient</a>
        </div>

    </div>
</div>
