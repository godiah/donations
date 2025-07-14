<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="-mt-4 form-group">
            <label for="name" class="form-label">{{ __('Full Name') }}</label>
            <input id="name" name="name" type="text" class="form-input" value="{{ old('name', $user->name) }}"
                required autofocus autocomplete="name" />
            <x-input-error class="form-error" :messages="$errors->get('name')" />
            <p class="form-hint">Your full name as it appears on official documents</p>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input id="email" name="email" type="email" class="form-input"
                value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="form-error" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="p-4 mt-3 border bg-secondary-50 border-secondary-200 rounded-xl">
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-secondary-500 rounded-full flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-medium text-neutral-800">
                                {{ __('Your email address is unverified.') }}</p>
                            <button form="send-verification"
                                class="text-sm font-medium underline rounded text-primary-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </div>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="p-3 mt-3 border rounded-lg bg-success-50 border-success-200">
                            <p class="text-sm font-medium text-success-700">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center pb-6 space-x-4">
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center space-x-2 text-sm text-success-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Profile updated successfully!') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
