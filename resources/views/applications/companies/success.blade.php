<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Application Submitted Successfully
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-8 bg-white">
                    <!-- Success Icon -->
                    <div class="flex justify-center mb-6">
                        <div class="p-4 bg-green-100 rounded-full">
                            <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div class="mb-8 text-center">
                        <h1 class="mb-4 text-3xl font-bold text-gray-900">Application Submitted Successfully!</h1>
                        <p class="mb-6 text-lg text-gray-600">
                            Your company donation application has been received and is now under review.
                        </p>
                    </div>

                    <!-- Application Details -->
                    <div class="p-6 mb-8 rounded-lg bg-gray-50">
                        <h2 class="mb-4 text-xl font-semibold text-gray-900">Application Details</h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Application Number</label>
                                <p class="mt-1 font-mono text-lg text-gray-900">{{ $application->application_number }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Submission Date</label>
                                <p class="mt-1 text-lg text-gray-900">
                                    {{ $application->submitted_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Contribution Name</label>
                                <p class="mt-1 text-lg text-gray-900">
                                    {{ $application->applicant->contribution_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Target Amount</label>
                                <p class="mt-1 text-lg text-gray-900">KES
                                    {{ number_format($application->applicant->target_amount ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Company Name</label>
                                <p class="mt-1 text-lg text-gray-900">
                                    {{ $application->applicant->company_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span
                                    class="inline-flex items-center px-3 py-1 text-sm font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                    {{ $application->status->value }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="p-6 mb-8 border border-blue-200 rounded-lg bg-blue-50">
                        <h3 class="mb-3 text-lg font-semibold text-blue-900">What Happens Next?</h3>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-6 h-6 bg-blue-600 rounded-full">
                                        <span class="text-sm font-bold text-white">1</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-blue-800">
                                        <strong>Review Process:</strong> Our team will review your company donation
                                        application and supporting documents within 3-5 business days.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-6 h-6 bg-blue-600 rounded-full">
                                        <span class="text-sm font-bold text-white">2</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-blue-800">
                                        <strong>Email Updates:</strong> You'll receive email notifications at
                                        <strong>{{ $application->applicant->contact_persons[0]['email'] ?? 'N/A' }}</strong>
                                        regarding your application status.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-6 h-6 bg-blue-600 rounded-full">
                                        <span class="text-sm font-bold text-white">3</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-blue-800">
                                        <strong>Approval & Launch:</strong> Once approved, your company donation
                                        campaign will go live, and you can start sharing it with potential donors.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="p-4 mb-8 border rounded-lg bg-amber-50 border-amber-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800">Important Notes</h3>
                                <div class="mt-2 text-sm text-amber-700">
                                    <ul class="space-y-1 list-disc list-inside">
                                        <li>Keep your application number
                                            <strong>{{ $application->application_number }}</strong> for future
                                            reference.
                                        </li>
                                        <li>Ensure your contact information is up to date to receive important updates.
                                        </li>
                                        <li>Additional documents may be requested during the review process.</li>
                                        <li>Campaign approval is subject to our terms and conditions.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col justify-center gap-4 sm:flex-row">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 0 002-2V9a2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5a2 2 0 012-2h2a2 2 0 012 2 2h0v2H8V5z"></path>
                            </svg>
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
