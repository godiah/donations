<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('New Payout Method') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-900">Add Payout Method</h1>
                    <p class="mt-1 text-sm text-gray-600">Set up how you want to receive donations</p>
                </div>

                <form method="POST" action="{{ route('payout-methods.store') }}" class="p-6">
                    @csrf

                    {{-- Payout Type Selection --}}
                    <div class="mb-6">
                        <label class="text-base font-medium text-gray-900">Select Payout Type</label>
                        <p class="text-sm leading-5 text-gray-500">Choose how you want to receive your donations</p>
                        <fieldset class="mt-4">
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input id="mobile_money" name="type" type="radio" value="mobile_money"
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                        {{ old('type', 'mobile_money') === 'mobile_money' ? 'checked' : '' }}>
                                    <label for="mobile_money" class="ml-3 block text-sm font-medium text-gray-700">
                                        Mobile Money
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="bank_account" name="type" type="radio" value="bank_account"
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                                        {{ old('type') === 'bank_account' ? 'checked' : '' }}>
                                    <label for="bank_account" class="ml-3 block text-sm font-medium text-gray-700">
                                        Bank Account
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mobile Money Fields --}}
                    <div id="mobile_money_fields" class="space-y-4"
                        style="{{ old('type', 'mobile_money') === 'mobile_money' ? 'display: block;' : 'display: none;' }}">
                        <div>
                            <label for="provider" class="block text-sm font-medium text-gray-700">Provider</label>
                            <select id="provider" name="provider"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Provider</option>
                                <option value="M-Pesa" {{ old('provider') === 'M-Pesa' ? 'selected' : '' }}>Safaricom
                                    M-Pesa</option>
                                <option value="Airtel Money" {{ old('provider') === 'Airtel Money' ? 'selected' : '' }}>
                                    Airtel Money</option>
                                <option value="T-Kash" {{ old('provider') === 'T-Kash' ? 'selected' : '' }}>Telkom
                                    T-Kash</option>
                            </select>
                            @error('provider')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700">Phone
                                Number</label>
                            <input type="text" id="account_number" name="account_number"
                                value="{{ old('account_number') }}" placeholder="e.g., 0712345678"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('account_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700">Account
                                Name</label>
                            <input type="text" id="account_name" name="account_name"
                                value="{{ old('account_name') }}"
                                placeholder="Name as registered with mobile money provider"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('account_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Bank Account Fields --}}
                    <div id="bank_account_fields" class="space-y-4" style="display: none;">
                        <div>
                            <label for="bank_id" class="block text-sm font-medium text-gray-700">Bank Name</label>
                            <select id="bank_id" name="bank_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}"
                                        {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700">Account
                                Number</label>
                            <input type="text" id="account_number" name="account_number"
                                value="{{ old('account_number') }}" placeholder="Your bank account number"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('account_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700">Account
                                Name</label>
                            <input type="text" id="account_name" name="account_name"
                                value="{{ old('account_name') }}" placeholder="Account holder name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('account_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Primary Method Checkbox --}}
                    <div class="mt-6">
                        <div class="flex items-center">
                            <input id="is_primary" name="is_primary" type="checkbox" value="1"
                                {{ old('is_primary') ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <label for="is_primary" class="ml-2 block text-sm text-gray-900">
                                Set as primary payout method
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">This will be your default method for receiving donations
                        </p>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('payout-methods.index') }}"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Add Payout Method
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeRadios = document.querySelectorAll('input[name="type"]');
            const mobileFields = document.getElementById('mobile_money_fields');
            const bankFields = document.getElementById('bank_account_fields');

            function toggleFields() {
                const selectedType = document.querySelector('input[name="type"]:checked');

                // Toggle visibility and disable/enable fields
                if (selectedType?.value === 'mobile_money') {
                    mobileFields.style.display = 'block';
                    bankFields.style.display = 'none';

                    // Enable mobile money fields
                    mobileFields.querySelectorAll('input, select').forEach(field => {
                        field.disabled = false;
                    });

                    // Disable bank account fields
                    bankFields.querySelectorAll('input, select').forEach(field => {
                        field.disabled = true;
                    });

                    // Clear bank fields
                    document.getElementById('bank_id').value = '';
                    bankFields.querySelectorAll('input').forEach(input => {
                        input.value = '';
                    });
                } else if (selectedType?.value === 'bank_account') {
                    mobileFields.style.display = 'none';
                    bankFields.style.display = 'block';

                    // Enable bank account fields
                    bankFields.querySelectorAll('input, select').forEach(field => {
                        field.disabled = false;
                    });

                    // Disable mobile money fields
                    mobileFields.querySelectorAll('input, select').forEach(field => {
                        field.disabled = true;
                    });

                    // Clear mobile money fields
                    document.getElementById('provider').value = '';
                    mobileFields.querySelectorAll('input').forEach(input => {
                        input.value = '';
                    });
                } else {
                    // If no type is selected, hide and disable both
                    mobileFields.style.display = 'none';
                    bankFields.style.display = 'none';
                    mobileFields.querySelectorAll('input, select').forEach(field => {
                        field.disabled = true;
                    });
                    bankFields.querySelectorAll('input, select').forEach(field => {
                        field.disabled = true;
                    });
                }
            }

            typeRadios.forEach(radio => radio.addEventListener('change', toggleFields));
            toggleFields(); // Initialize on load
        });
    </script>

</x-app-layout>
