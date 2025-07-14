@if ($applications->count() > 0)
    <div class="modern-table">
        <div class="table-header">
            <div class="flex items-center space-x-3">
                <div
                    class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-neutral-600 to-neutral-700 rounded-xl">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold font-heading text-neutral-800">Applications List</h3>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                            Application #</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                            Type</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                            Applicant</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                            Contribution</th>
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                            Status</th>
                        {{-- <th class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                            Submitted</th> --}}
                        <th class="px-6 py-4 text-xs font-medium tracking-wider text-left uppercase text-neutral-500">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($applications as $application)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <code class="px-2 py-1 font-mono text-sm rounded bg-neutral-100 text-neutral-700">
                                        {{ $application->application_number }}
                                    </code>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if ($application->applicant_type === 'App\\Models\\Individual')
                                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-100">
                                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-neutral-800">Individual</span>
                                    @else
                                        <div
                                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-secondary-100">
                                            <svg class="w-4 h-4 text-secondary-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-neutral-800">Company</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if ($application->applicant_type === 'App\\Models\\Individual')
                                        <div class="text-sm font-medium text-neutral-800">
                                            {{ $application->applicant->getFullNameAttribute() }}</div>
                                        <div class="text-xs text-neutral-500">{{ $application->applicant->email }}</div>
                                    @else
                                        <div class="text-sm font-medium text-neutral-800">
                                            {{ $application->applicant->company_name }}</div>
                                        <div class="text-xs text-neutral-500">
                                            {{ $application->applicant->email ?? 'No email available' }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="text-sm font-medium text-neutral-800">
                                        {{ Str::limit($application->applicant->contribution_name, 30) }}</div>
                                    @if ($application->applicant->target_amount)
                                        <div class="text-xs text-neutral-500">
                                            Target: <span class="font-medium text-success-600">KES
                                                {{ number_format($application->applicant->target_amount, 2) }}</span>
                                        </div>
                                    @else
                                        <div class="text-xs text-neutral-500">
                                            No Target </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusEnum = \App\Enums\ApplicationStatus::fromValue($application->status->value);
                                @endphp
                                <span
                                    class="status-badge {{ $statusEnum ? $statusEnum->getColorClass() : 'bg-neutral-100 text-neutral-800' }}">
                                    @if ($statusEnum)
                                        {!! $statusEnum->getIcon() !!}
                                        <span class="ml-1">{{ $statusEnum->getDisplayName() }}</span>
                                    @else
                                        {{ ucfirst($application->status->value) }}
                                    @endif
                                </span>
                            </td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                @if ($application->submitted_at)
                                    <div class="space-y-1">
                                        <div class="text-sm font-medium text-neutral-800">
                                            {{ $application->submitted_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-neutral-500">
                                            {{ $application->submitted_at->format('H:i') }}</div>
                                    </div>
                                @else
                                    <span class="text-xs italic text-neutral-400">Not submitted</span>
                                @endif
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.applications.show', $application) }}" class="action-button">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        @if ($applications->hasPages())
            <div class="px-8 py-6 border-t bg-neutral-50 border-neutral-100">
                {{ $applications->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@else
    <!-- Enhanced Empty State -->
    <div class="overflow-hidden bg-white border shadow-lg rounded-2xl border-neutral-100">
        <div class="py-16 text-center">
            <div
                class="flex items-center justify-center w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-2xl">
                <svg class="w-10 h-10 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="mb-2 text-xl font-bold font-heading text-neutral-800">No applications found</h3>
            <p class="max-w-md mx-auto text-neutral-600">Applications matching your selected criteria will appear here
                once they're submitted.</p>
        </div>
    </div>
@endif
