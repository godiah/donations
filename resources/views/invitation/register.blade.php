<x-guest-layout>
    <div class="w-full max-w-md p-8">
        <h2 class="mb-6 text-2xl font-bold text-center text-gray-600">Complete Your Registration</h2>

        <form method="POST" action="{{ route('invitation.register.submit', $invitation->token) }}">
            @csrf
            @method('POST')

            <!-- Name (Read-only) -->
            <div class="mb-4">
                <label for="name" class="block font-semibold text-gray-700">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $invitation->name) }}" readonly
                    class="block w-full p-2 mt-1 bg-gray-100 border border-gray-300 rounded-md">
            </div>

            <!-- Email (Read-only) -->
            <div class="mb-4">
                <label for="email" class="block font-semibold text-gray-700">Email</label>
                <input type="text" id="email" name="email" value="{{ old('email', $invitation->email) }}"
                    readonly class="block w-full p-2 mt-1 bg-gray-100 border border-gray-300 rounded-md">
            </div>

            <!-- User Type (Read-only) -->
            <div class="mb-4">
                <label for="user_type" class="block font-semibold text-gray-700">User Type</label>
                <input type="text" id="user_type" name="user_type"
                    value="{{ Illuminate\Support\Str::title(old('user_type', $user_type)) }}" readonly
                    class="block w-full p-2 mt-1 bg-gray-100 border border-gray-300 rounded-md">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block font-semibold text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                    class="block w-full p-2 mt-1 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block font-semibold text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="block w-full p-2 mt-1 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full px-4 py-2 font-semibold text-white transition duration-200 bg-blue-600 rounded-md hover:bg-blue-700">
                Register
            </button>
        </form>
    </div>
</x-guest-layout>
