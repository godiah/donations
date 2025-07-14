<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="py-2 space-y-1">
                <h2 class="text-2xl font-bold font-heading text-neutral-800">
                    Application Management
                </h2>
                <p class="text-sm font-medium text-neutral-500">
                    Review and manage donation applications across the platform
                </p>
            </div>
            <div class="items-center hidden space-x-2 text-sm sm:flex text-neutral-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Admin Panel</span>
            </div>
        </div>
    </x-slot>

    <div class="pt-6 pb-8">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Overview Stats Cards -->
            <div class="grid grid-cols-1 gap-6 mb-4 md:grid-cols-2">
                <div
                    class="p-6 border shadow-lg bg-gradient-to-br from-primary-50 via-white to-primary-50 rounded-2xl border-primary-100">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-medium text-neutral-500">Individual Applications</p>
                            <p class="text-2xl font-bold font-heading text-neutral-800">
                                {{ $counts['individual']['all'] }}</p>
                        </div>
                    </div>
                </div>

                <div
                    class="p-6 border shadow-lg bg-gradient-to-br from-secondary-50 via-white to-secondary-50 rounded-2xl border-secondary-100">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-medium text-neutral-500">Company Applications</p>
                            <p class="text-2xl font-bold font-heading text-neutral-800">{{ $counts['company']['all'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Type Tabs -->
            <div class="mb-4 overflow-hidden bg-white border shadow-lg rounded-2xl border-neutral-100">
                <div class="px-8 py-4 border-b bg-gradient-to-r from-neutral-50 to-white border-neutral-100">
                    <nav class="flex space-x-2">
                        <button onclick="switchMainTab('individual')"
                            class="main-tab {{ $type === 'individual' || $type === 'all' ? 'tab-active' : 'tab-inactive' }} inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Individual Applications
                            <span
                                class="ml-2 bg-white/80 text-neutral-700 rounded-full px-2 py-0.5 text-xs font-medium">{{ $counts['individual']['all'] }}</span>
                        </button>
                        <button onclick="switchMainTab('company')"
                            class="main-tab {{ $type === 'company' ? 'tab-active' : 'tab-inactive' }} inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Company Applications
                            <span
                                class="ml-2 bg-white/80 text-neutral-700 rounded-full px-2 py-0.5 text-xs font-medium">{{ $counts['company']['all'] }}</span>
                        </button>
                    </nav>
                </div>

                <!--  Status Sub-tabs -->
                <div class="px-8 py-4 bg-gradient-to-r from-white to-neutral-50">
                    <nav class="flex flex-wrap gap-2">
                        <button onclick="switchStatusTab('all')"
                            class="status-tab {{ $status === 'all' ? 'status-active' : 'status-inactive' }} inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                            {!! \App\Enums\ApplicationStatus::getAllIcon() !!}
                            <span class="ml-2">All</span>
                            <span
                                class="ml-2 bg-neutral-100 text-neutral-700 rounded-full px-2 py-0.5 text-xs font-medium status-count"
                                data-type="all">
                                {{ $type === 'individual' ? $counts['individual']['all'] : ($type === 'company' ? $counts['company']['all'] : $counts['total']['all']) }}
                            </span>
                        </button>

                        @foreach (\App\Enums\ApplicationStatus::cases() as $statusEnum)
                            @if ($statusEnum->value !== 'draft')
                                <button onclick="switchStatusTab('{{ $statusEnum->value }}')"
                                    class="status-tab {{ $status === $statusEnum->value ? 'status-active' : 'status-inactive' }} inline-flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                                    {!! $statusEnum->getIcon() !!}
                                    <span class="ml-2">{{ $statusEnum->getDisplayName() }}</span>
                                    <span
                                        class="ml-2 rounded-full px-2 py-0.5 text-xs font-medium status-count
                                        {{ $status === $statusEnum->value ? 'bg-white/80 text-neutral-700' : 'bg-neutral-100 text-neutral-700' }}"
                                        data-type="{{ $statusEnum->value }}">
                                        {{ $type === 'individual' ? $counts['individual'][$statusEnum->value] : ($type === 'company' ? $counts['company'][$statusEnum->value] : $counts['total'][$statusEnum->value]) }}
                                    </span>
                                </button>
                            @endif
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Applications Table Container -->
            <div id="applications-container">
                @include('admin.applications.partials.applications-table', [
                    'applications' => $applications,
                ])
            </div>
        </div>
    </div>

    <script>
        let currentType = '{{ $type }}';
        let currentStatus = '{{ $status }}';

        function switchMainTab(type) {
            currentType = type;
            updateTabs();
            loadApplications();
        }

        function switchStatusTab(status) {
            currentStatus = status;
            updateTabs();
            loadApplications();
        }

        function updateTabs() {
            // Update main tab styling
            document.querySelectorAll('.main-tab').forEach(tab => {
                tab.classList.remove('tab-active');
                tab.classList.add('tab-inactive');
            });

            // Update status tab styling
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.classList.remove('status-active');
                tab.classList.add('status-inactive');
            });

            // Activate current tabs
            if (event.target.closest('.main-tab')) {
                event.target.closest('.main-tab').classList.remove('tab-inactive');
                event.target.closest('.main-tab').classList.add('tab-active');
            }

            if (event.target.closest('.status-tab')) {
                event.target.closest('.status-tab').classList.remove('status-inactive');
                event.target.closest('.status-tab').classList.add('status-active');
            }
        }

        function loadApplications() {
            const url = `{{ route('admin.applications.index') }}?type=${currentType}&status=${currentStatus}`;

            // Show loading state
            const container = document.querySelector('#applications-container');
            container.innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 border-4 rounded-full border-primary-200 border-t-primary-500 animate-spin"></div>
                        <span class="font-medium text-neutral-600">Loading applications...</span>
                    </div>
                </div>
            `;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('#applications-container');
                    container.innerHTML = newTable.innerHTML;
                })
                .catch(error => {
                    console.error('Error loading applications:', error);
                    container.innerHTML = `
                        <div class="py-12 text-center">
                            <div class="font-medium text-danger-600">Error loading applications</div>
                            <div class="mt-2 text-sm text-neutral-500">Please try again</div>
                        </div>
                    `;
                });
        }
    </script>
</x-app-layout>
