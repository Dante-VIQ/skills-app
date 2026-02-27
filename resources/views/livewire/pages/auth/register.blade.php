<div class="max-w-2xl mx-auto">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">1</div>
                <span class="ml-2 text-sm font-medium text-gray-700">Account Type</span>
            </div>
            <div class="flex-1 mx-4 h-1 bg-gray-200 rounded">
                <div class="w-1/3 h-full bg-indigo-600 rounded"></div>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-bold">2</div>
                <span class="ml-2 text-sm font-medium text-gray-400">Details</span>
            </div>
        </div>
    </div>

    <form wire:submit="register">
        <!-- Role Selection -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 mb-4">I am a...</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Parent -->
                <button type="button" 
                        wire:click="$set('user_role', 'parent')"
                        class="relative p-6 border-2 rounded-xl transition-all duration-200
                               {{ $user_role === 'parent' ? 'border-indigo-600 bg-indigo-50 ring-4 ring-indigo-100' : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50' }}">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Parent</h3>
                        <p class="text-xs text-gray-500 mt-1">Free account</p>
                    </div>
                    @if($user_role === 'parent')
                        <div class="absolute top-2 right-2">
                            <i class="fas fa-check-circle text-indigo-600 text-xl"></i>
                        </div>
                    @endif
                </button>

                <!-- Child -->
                <button type="button" 
                        wire:click="$set('user_role', 'child')"
                        class="relative p-6 border-2 rounded-xl transition-all duration-200
                               {{ $user_role === 'child' ? 'border-green-600 bg-green-50 ring-4 ring-green-100' : 'border-gray-200 hover:border-green-300 hover:bg-gray-50' }}">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-child text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Student</h3>
                        <p class="text-xs text-gray-500 mt-1">Need parent code</p>
                    </div>
                    @if($user_role === 'child')
                        <div class="absolute top-2 right-2">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    @endif
                </button>

                <!-- Tutor -->
                <button type="button" 
                        wire:click="$set('user_role', 'tutor')"
                        class="relative p-6 border-2 rounded-xl transition-all duration-200
                               {{ $user_role === 'tutor' ? 'border-purple-600 bg-purple-50 ring-4 ring-purple-100' : 'border-gray-200 hover:border-purple-300 hover:bg-gray-50' }}">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Tutor</h3>
                        <p class="text-xs text-gray-500 mt-1">Invitation only</p>
                    </div>
                    @if($user_role === 'tutor')
                        <div class="absolute top-2 right-2">
                            <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                        </div>
                    @endif
                </button>
            </div>
            @error('user_role') 
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Personal Information -->
        <div class="bg-gray-50 p-6 rounded-xl mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" 
                           wire:model="first_name" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="John">
                    @error('first_name') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" 
                           wire:model="last_name" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Doe">
                    @error('last_name') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-gray-50 p-6 rounded-xl mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" 
                           wire:model="email" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="you@example.com">
                    @error('email') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" 
                           wire:model="username" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="cooluser123">
                    @error('username') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" 
                               wire:model="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••••">
                        @error('password') 
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" 
                               wire:model="password_confirmation" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>
        </div>

        <!-- Child Registration Fields -->
        @if($show_child_fields)
        <div class="bg-green-50 p-6 rounded-xl mb-6 border-2 border-green-200">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-key text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-green-800 ml-3">Parent Invitation Required</h3>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-green-800 mb-2">Parent Invitation Code</label>
                <input type="text" 
                       wire:model.live="parent_invite_code" 
                       class="w-full px-4 py-3 border-2 border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="Enter 8-digit code"
                       maxlength="8">
                <p class="text-sm text-green-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ask your parent for this code
                </p>
                @error('parent_invite_code') 
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
                
                @if($parent_name)
                    <div class="mt-4 p-3 bg-green-100 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 text-xl mr-2"></i>
                            <div>
                                <p class="text-green-800 font-medium">Code Verified!</p>
                                <p class="text-green-700 text-sm">Parent: {{ $parent_name }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Tutor Registration Fields -->
        @if($show_tutor_fields)
        <div class="bg-purple-50 p-6 rounded-xl mb-6 border-2 border-purple-200">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-id-card text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-purple-800 ml-3">Tutor Invitation Required</h3>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-purple-800 mb-2">Tutor Invitation Code</label>
                <input type="text" 
                       wire:model="tutor_invite_code" 
                       class="w-full px-4 py-3 border-2 border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="Enter 12-digit code"
                       maxlength="12">
                <p class="text-sm text-purple-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Provided by SkillDNA administration
                </p>
                @error('tutor_invite_code') 
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>
        @endif

        <!-- Terms & Submit -->
        <div class="space-y-6">
            <div class="flex items-start">
                <input type="checkbox" id="terms" class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" required>
                <label for="terms" class="ml-2 text-sm text-gray-600">
                    I agree to the 
                    <a href="{{ route('terms') }}" class="text-indigo-600 hover:text-indigo-800" target="_blank">Terms of Service</a> 
                    and 
                    <a href="{{ route('privacy') }}" class="text-indigo-600 hover:text-indigo-800" target="_blank">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    wire:target="register">
                <span wire:loading.remove wire:target="register">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </span>
                <span wire:loading wire:target="register">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Creating Account...
                </span>
            </button>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </form>
</div>