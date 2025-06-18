<x-guest-layout>
    <form id="registrationForm" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div id="nameError" class="mt-2 text-sm text-red-600" style="display: none;"></div>
        </div>

        <!-- User Type -->
        <div class="mt-4">
            <x-input-label for="user_type" :value="__('Registration Type')" />
            <select id="user_type" name="user_type"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>
                <option value="">{{ __('Select registration type') }}</option>
                <option value="individual" {{ old('user_type') == 'individual' ? 'selected' : '' }}>
                    {{ __('Individual') }}
                </option>
                <option value="company" {{ old('user_type') == 'company' ? 'selected' : '' }}>
                    {{ __('Company') }}
                </option>
            </select>
            <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
            <div id="user_typeError" class="mt-2 text-sm text-red-600" style="display: none;"></div>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div id="emailError" class="mt-2 text-sm text-red-600" style="display: none;"></div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div id="passwordError" class="mt-2 text-sm text-red-600" style="display: none;"></div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <div id="password_confirmationError" class="mt-2 text-sm text-red-600" style="display: none;"></div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button id="submitButton" class="ms-4" type="submit">
                <span id="buttonText">Register</span>
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
            const form = document.getElementById('registrationForm');
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
                    name: formData.get('name'),
                    email: formData.get('email'),
                    user_type: formData.get('user_type'),
                    password: formData.get('password'),
                    password_confirmation: formData.get('password_confirmation')
                };

                try {
                    await axios.get('/sanctum/csrf-cookie', {
                        withCredentials: true
                    });
                    const xsrfToken = document.cookie
                        .split('; ')
                        .find(row => row.startsWith('XSRF-TOKEN='))
                        ?.split('=')[1];

                    const response = await axios.post('/api/auth/register', data, {
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
                showAlert('success', result.message || 'Registration successful! Redirecting...', {
                    timer: 1000,
                    willClose: () => {
                        window.location.href = '/dashboard';
                    }
                });
            }

            function handleErrors(result) {
                if (result.errors) {
                    let hasFieldErrors = false;
                    Object.keys(result.errors).forEach(field => {
                        const errorElement = document.getElementById(field + 'Error');
                        if (errorElement) {
                            errorElement.textContent = Array.isArray(result.errors[field]) ?
                                result.errors[field][0] :
                                result.errors[field];
                            errorElement.style.display = 'block';
                            hasFieldErrors = true;
                        }
                    });

                    if (hasFieldErrors) {
                        showAlert('error', 'Please correct the errors below.');
                    } else {
                        showAlert('error', result.message || 'Validation failed. Please check your information.');
                    }
                } else {
                    showAlert('error', result.message || 'Registration failed. Please check your information.');
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
                buttonText.textContent = loading ? 'Registering...' : 'Register';
                buttonLoader.style.display = loading ? 'inline-block' : 'none';
                form.style.opacity = loading ? '0.7' : '1';
                form.style.pointerEvents = loading ? 'none' : 'auto';
            }
        });
    </script>
</x-guest-layout>
