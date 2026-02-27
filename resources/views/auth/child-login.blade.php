<x-guest-layout>
    <form method="POST" action="{{ route('child.login.submit') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input id="username" type="username" name="username" value="{{ old('username') }}" required autocomplete="username" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
            @error('username')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="remember" class="inline-flex items-center">
                <input id="remember" type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-500">
            Login as Child
        </button>

        @if($errors->any())
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                <p class="text-red-600 text-sm">Login failed. Please check your credentials.</p>
            </div>
        @endif
    </form>

</x-guest-layout>