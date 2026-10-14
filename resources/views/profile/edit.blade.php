@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
            <p class="text-gray-600 mt-1">Manage your account information and settings.</p>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                Profile updated successfully.
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                Password updated successfully.
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Left: Avatar -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->first_name }}"
                             class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-indigo-100">
                    @else
                        <div class="w-32 h-32 rounded-full mx-auto bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold">
                            {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                        </div>
                    @endif

                    <h2 class="mt-4 font-semibold text-lg">{{ $user->first_name }} {{ $user->last_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400 mt-1 capitalize">{{ $user->type ?? 'user' }}</p>

                    @if($user->avatar)
                        <form method="POST" action="{{ route('profile.avatar.delete') }}" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                <i class="fas fa-trash mr-1"></i> Remove Avatar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Right: Forms -->
            <div class="md:col-span-2 space-y-8">
                <!-- Profile Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h3>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                       class="w-full rounded-lg border-gray-300" required>
                                @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                       class="w-full rounded-lg border-gray-300" required>
                                @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full rounded-lg border-gray-300" required>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($user->username)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                   class="w-full rounded-lg border-gray-300">
                            @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Avatar (optional)</label>
                            <input type="file" name="avatar" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" name="current_password" class="w-full rounded-lg border-gray-300" required>
                            @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" class="w-full rounded-lg border-gray-300" required>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-300" required>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection