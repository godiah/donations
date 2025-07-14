<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="py-2 space-y-1">
                <h2 class="text-2xl font-bold font-heading text-neutral-800">
                    {{ __('Profile Settings') }}
                </h2>
                <p class="text-sm font-medium text-neutral-500">
                    Manage your account information and security settings
                </p>
            </div>
            <div class="items-center hidden space-x-2 text-sm sm:flex text-neutral-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Account Management</span>
            </div>
        </div>
    </x-slot>

    <div class="pt-6 pb-8">
        <div class="max-w-4xl px-4 mx-auto space-y-8 sm:px-6 lg:px-8">
            <!-- Profile Information Section -->
            <div class="overflow-hidden bg-white border shadow-lg rounded-2xl border-neutral-100">
                <div class="px-8 py-6 border-b bg-gradient-to-r from-primary-50 to-blue-50 border-neutral-100">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold font-heading text-neutral-800">{{ __('Profile Information') }}
                            </h2>
                            <p class="text-sm text-neutral-600">
                                {{ __("Update your account's profile information and email address.") }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-8">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Password Update Section -->
            <div class="overflow-hidden bg-white border shadow-lg rounded-2xl border-neutral-100">
                <div class="px-8 py-6 border-b bg-gradient-to-r from-secondary-50 to-orange-50 border-neutral-100">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold font-heading text-neutral-800">{{ __('Update Password') }}</h2>
                            <p class="text-sm text-neutral-600">
                                {{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-8">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Delete Account Section -->
            <div class="overflow-hidden bg-white border shadow-lg rounded-2xl border-danger-200">
                <div class="px-8 py-6 border-b bg-gradient-to-r from-danger-50 to-red-50 border-danger-100">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-danger-500 to-danger-600 rounded-xl">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold font-heading text-neutral-800">{{ __('Delete Account') }}</h2>
                            <p class="text-sm text-neutral-600">
                                {{ __('Permanently remove your account and all associated data.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
