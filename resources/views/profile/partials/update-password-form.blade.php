<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-input"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="form-error" />
            <p class="form-hint">Enter your existing password for security verification</p>
        </div>

        <div class="form-group">
            <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="form-input"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="form-error" />
            <p class="form-hint">Use at least 8 characters with a mix of letters, numbers, and symbols</p>
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation"
                class="form-label">{{ __('Confirm New Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="form-error" />
            <p class="form-hint">Re-enter your new password to confirm</p>
        </div>

        <div class="flex items-center pb-6 space-x-4">
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center space-x-2 text-sm text-success-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Password updated successfully!') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
