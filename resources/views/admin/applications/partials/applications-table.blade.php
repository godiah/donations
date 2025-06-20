@if ($applications->count() > 0)
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Application #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Contribution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($applications as $application)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $application->application_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $application->applicant_type === 'App\\Models\\Individual' ? 'Individual' : 'Company' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if ($application->applicant_type === 'App\\Models\\Individual')
                                {{ $application->applicant->full_name }}
                                <div class="text-xs text-gray-500">{{ $application->applicant->email }}</div>
                            @else
                                {{ $application->applicant->company_name }}
                                <div class="text-xs text-gray-500">{{ $application->user->email }}</div>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if ($application->submitted_at)
                                {{ $application->submitted_at->format('M d, Y') }}
                                <div class="text-xs">{{ $application->submitted_at->format('H:i') }}</div>
                            @else
                                <span class="text-gray-400">Not submitted</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
    <div class="text-center py-12">
        <div class="text-gray-500 text-lg">No applications found</div>
        <div class="text-gray-400 text-sm mt-2">Applications matching your criteria will appear here</div>
    </div>
@endif
