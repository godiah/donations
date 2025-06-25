@if ($applications->count() > 0)
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Application #</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Applicant
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        Contribution</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Submitted
                    </th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($applications as $application)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                            {{ $application->application_number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $application->applicant_type === 'App\\Models\\Individual' ? 'Individual' : 'Company' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            @if ($application->applicant_type === 'App\\Models\\Individual')
                                {{ $application->applicant->getFullNameAttribute() }}
                                <div class="text-xs text-gray-500">{{ $application->applicant->email }}</div>
                            @else
                                {{ $application->applicant->company_name }}
                                <div class="text-xs text-gray-500">
                                    {{ $application->applicant->email ?? 'No email available' }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ Str::limit($application->applicant->contribution_name, 30) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Target: KES {{ number_format($application->applicant->target_amount, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match ($application->status->value) {
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'under_review' => 'bg-yellow-100 text-yellow-800',
                                    'additional_info_required' => 'bg-orange-100 text-orange-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                                $statusText = match ($application->status->value) {
                                    'under_review' => 'Under Review',
                                    'additional_info_required' => 'Additional Info Required',
                                    default => ucfirst($application->status->value),
                                };
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            @if ($application->submitted_at)
                                {{ $application->submitted_at->format('M d, Y') }}
                                <div class="text-xs">{{ $application->submitted_at->format('H:i') }}</div>
                            @else
                                <span class="text-gray-400">Not submitted</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            <a href="{{ route('admin.applications.show', $application) }}"
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                View Details
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($applications->hasPages())
        <div class="mt-6">
            {{ $applications->appends(request()->query())->links() }}
        </div>
    @endif
@else
    <div class="py-12 text-center">
        <div class="text-lg text-gray-500">No applications found</div>
        <div class="mt-2 text-sm text-gray-400">Applications matching your criteria will appear here</div>
    </div>
@endif
