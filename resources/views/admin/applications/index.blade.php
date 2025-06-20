<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Applications Management
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Main Type Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="switchMainTab('individual')"
                        class="main-tab {{ $type === 'individual' || $type === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Individual Applications
                        <span
                            class="ml-2 bg-gray-100 text-gray-900 rounded-full px-2.5 py-0.5 text-xs font-medium">{{ $counts['individual']['all'] }}</span>
                    </button>
                    <button onclick="switchMainTab('company')"
                        class="main-tab {{ $type === 'company' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Company Applications
                        <span
                            class="ml-2 bg-gray-100 text-gray-900 rounded-full px-2.5 py-0.5 text-xs font-medium">{{ $counts['company']['all'] }}</span>
                    </button>
                </nav>
            </div>

            <!-- Status Sub-tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="switchStatusTab('all')"
                        class="status-tab {{ $status === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        All
                        <span
                            class="ml-2 bg-gray-100 text-gray-900 rounded-full px-2.5 py-0.5 text-xs font-medium status-count"
                            data-type="all">
                            {{ $type === 'individual' ? $counts['individual']['all'] : ($type === 'company' ? $counts['company']['all'] : $counts['total']['all']) }}
                        </span>
                    </button>
                    <button onclick="switchStatusTab('submitted')"
                        class="status-tab {{ $status === 'submitted' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Submitted
                        <span
                            class="ml-2 bg-gray-100 text-gray-900 rounded-full px-2.5 py-0.5 text-xs font-medium status-count"
                            data-type="submitted">
                            {{ $type === 'individual' ? $counts['individual']['submitted'] : ($type === 'company' ? $counts['company']['submitted'] : $counts['total']['submitted']) }}
                        </span>
                    </button>
                    <button onclick="switchStatusTab('under_review')"
                        class="status-tab {{ $status === 'under_review' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Under Review
                        <span
                            class="ml-2 bg-gray-100 text-gray-900 rounded-full px-2.5 py-0.5 text-xs font-medium status-count"
                            data-type="under_review">
                            {{ $type === 'individual' ? $counts['individual']['under_review'] : ($type === 'company' ? $counts['company']['under_review'] : $counts['total']['under_review']) }}
                        </span>
                    </button>
                    <button onclick="switchStatusTab('additional_info')"
                        class="status-tab {{ $status === 'additional_info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Additional Info
                        <span
                            class="ml-2 bg-yellow-100 text-yellow-800 rounded-full px-2.5 py-0.5 text-xs font-medium status-count"
                            data-type="additional_info">
                            {{ $type === 'individual' ? $counts['individual']['additional_info'] : ($type === 'company' ? $counts['company']['additional_info'] : $counts['total']['additional_info']) }}
                        </span>
                    </button>
                    <button onclick="switchStatusTab('approved')"
                        class="status-tab {{ $status === 'approved' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Approved
                        <span
                            class="ml-2 bg-green-100 text-green-800 rounded-full px-2.5 py-0.5 text-xs font-medium status-count"
                            data-type="approved">
                            {{ $type === 'individual' ? $counts['individual']['approved'] : ($type === 'company' ? $counts['company']['approved'] : $counts['total']['approved']) }}
                        </span>
                    </button>
                    <button onclick="switchStatusTab('rejected')"
                        class="status-tab {{ $status === 'rejected' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        Rejected
                        <span
                            class="ml-2 bg-red-100 text-red-800 rounded-full px-2.5 py-0.5 text-xs font-medium status-count"
                            data-type="rejected">
                            {{ $type === 'individual' ? $counts['individual']['rejected'] : ($type === 'company' ? $counts['company']['rejected'] : $counts['total']['rejected']) }}
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Applications Table -->
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
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });

            // Update status tab styling
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });

            // Activate current tabs
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-blue-500', 'text-blue-600');
        }

        function loadApplications() {
            const url = `{{ route('admin.applications.index') }}?type=${currentType}&status=${currentStatus}`;

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
                    document.querySelector('#applications-container').innerHTML = newTable.innerHTML;
                });
        }
    </script>
</x-app-layout>
