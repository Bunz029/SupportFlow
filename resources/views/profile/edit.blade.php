<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile Settings') }}
        </h2>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Profile Information Section -->
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
                            @if (session('status') === 'profile-updated')
                                <div class="text-sm font-medium text-green-600">Profile updated successfully!</div>
                            @endif
                        </div>
    
                        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>
    
                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
    
                                <!-- Phone -->
                                <div>
                                    <x-input-label for="phone" :value="__('Phone')" />
                                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>
    
                                <!-- Company -->
                                <div>
                                    <x-input-label for="company" :value="__('Company')" />
                                    <x-text-input id="company" name="company" type="text" class="mt-1 block w-full" :value="old('company', $user->company)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('company')" />
                                </div>
    
                                <!-- Job Title -->
                                <div>
                                    <x-input-label for="job_title" :value="__('Job Title')" />
                                    <x-text-input id="job_title" name="job_title" type="text" class="mt-1 block w-full" :value="old('job_title', $user->job_title)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('job_title')" />
                                </div>
    
                                <!-- Department -->
                                <div>
                                    <x-input-label for="department" :value="__('Department')" />
                                    <x-text-input id="department" name="department" type="text" class="mt-1 block w-full" :value="old('department', $user->department)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('department')" />
                                </div>
                            </div>
    
                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button>
                                    {{ __('Save Profile') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
    
                    <!-- Update Password Section -->
                    <div class="p-6 bg-white">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Security Settings</h3>
                            @if (session('status') === 'password-updated')
                                <div class="text-sm font-medium text-green-600">Password updated successfully!</div>
                            @endif
                        </div>
    
                        <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            @method('PUT')
    
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Current Password -->
                                <div>
                                    <x-input-label for="current_password" :value="__('Current Password')" />
                                    <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                </div>
    
                                <!-- New Password -->
                                <div>
                                    <x-input-label for="password" :value="__('New Password')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>
    
                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
    
                            <div class="flex items-center justify-end mt-6">
                                <x-secondary-button type="submit" class="bg-green-600 hover:bg-green-700">
                                    {{ __('Change Password') }}
                                </x-secondary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

</x-app-layout>