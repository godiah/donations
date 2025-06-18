<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="loginForm" method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div id="emailError" class="mt-2 text-sm text-red-600" style="display: none;"></div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div id="passwordError" class="mt-2 text-sm text-red-600" style="display: none;"></div>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button id="submitButton" class="ms-3" type="submit">
                <span id="buttonText">{{ __('Log in') }}</span>
                <span id="buttonLoader" style="display: none;" class="ml-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitButton = document.getElementById('submitButton');
            const buttonText = document.getElementById('buttonText');
            const buttonLoader = document.getElementById('buttonLoader');

            form.addEventListener('submit', handleAPISubmission);

            async function handleAPISubmission(e) {
                e.preventDefault();
                clearErrors();
                setLoadingState(true);

                const formData = new FormData(form);
                const data = {
                    email: formData.get('email'),
                    password: formData.get('password'),
                    remember: formData.get('remember') ? true : false
                };

                try {
                    // Fetch CSRF cookie
                    await axios.get('/sanctum/csrf-cookie', {
                        withCredentials: true
                    });
                    // Get XSRF-TOKEN from cookies
                    const xsrfToken = document.cookie
                        .split('; ')
                        .find(row => row.startsWith('XSRF-TOKEN='))
                        ?.split('=')[1];

                    const response = await axios.post('/api/auth/login', data, {
                        withCredentials: true,
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-XSRF-TOKEN': xsrfToken ? decodeURIComponent(xsrfToken) : ''
                        }
                    });

                    handleSuccess(response.data);
                } catch (error) {
                    if (error.response) {
                        handleErrors(error.response.data);
                    } else {
                        showAlert('error', 'Network error. Please check your connection.');
                    }
                } finally {
                    setLoadingState(false);
                }
            }

            function handleSuccess(result) {
                showAlert('success', result.message || 'Login successful! Redirecting...', {
                    timer: 1000,
                    willClose: () => {
                        window.location.href = '/dashboard';
                    }
                });
            }

            function handleErrors(result) {
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const errorElement = document.getElementById(field + 'Error');
                        if (errorElement) {
                            errorElement.textContent = Array.isArray(result.errors[field]) ?
                                result.errors[field][0] :
                                result.errors[field];
                            errorElement.style.display = 'block';
                        }
                    });
                    showAlert('error', 'Please correct the errors below.');
                } else {
                    showAlert('error', result.message || 'Login failed. Please check your credentials.');
                }
            }

            function clearErrors() {
                const errorElements = document.querySelectorAll('[id$="Error"]');
                errorElements.forEach(element => {
                    element.style.display = 'none';
                    element.textContent = '';
                });
            }

            function setLoadingState(loading) {
                submitButton.disabled = loading;
                buttonText.textContent = loading ? 'Logging in...' : 'Log in';
                buttonLoader.style.display = loading ? 'inline-block' : 'none';
                form.style.opacity = loading ? '0.7' : '1';
                form.style.pointerEvents = loading ? 'none' : 'auto';
            }
        });
    </script>
</x-guest-layout>
