<section class="space-y-6">
    <div class="p-6 border bg-gradient-to-r from-danger-50 to-red-50 border-danger-200 rounded-2xl">
        <div class="flex items-start space-x-4">
            <div class="w-8 h-8 bg-danger-500 rounded-xl flex items-center justify-center mt-0.5">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="mb-2 font-bold font-heading text-neutral-800">{{ __('Permanent Account Deletion') }}</h3>
                <p class="mb-4 text-sm leading-relaxed text-neutral-700">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                </p>
                <ul class="space-y-1 text-sm text-neutral-600">
                    <li class="flex items-center space-x-2">
                        <svg class="w-3 h-3 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>All donation campaigns will be permanently removed</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-3 h-3 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Transaction history will be lost</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-3 h-3 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>This action cannot be undone</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="flex justify-start">
        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="btn-danger">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            {{ __('Delete Account') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="overflow-hidden bg-white border shadow-2xl rounded-2xl border-neutral-100">
            <div class="px-8 py-6 bg-gradient-to-r from-danger-500 to-danger-600">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-white rounded-2xl">
                        <svg class="w-6 h-6 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white font-heading">
                            {{ __('Are you sure you want to delete your account?') }}</h2>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
                @csrf
                @method('delete')

                <div class="mb-6">
                    <p class="leading-relaxed text-neutral-700">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                </div>

                <div class="form-group">
                    <label for="password" class="sr-only form-label">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password" class="form-input"
                        placeholder="{{ __('Enter your password to confirm') }}" />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="form-error" />
                </div>

                <div class="flex justify-end pt-6 space-x-4">
                    <button type="button" x-on:click="$dispatch('close')" class="btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="btn-danger">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>
