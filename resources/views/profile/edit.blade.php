<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[rgb(30,64,175)] leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 font-[Poppins,sans-serif]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Update Profile Information -->
            <div class="p-1 rounded-2xl shadow
                        bg-gradient-to-br from-[rgb(219,234,254)]
                                           via-[rgb(59,130,246)]
                                           to-[rgb(30,64,175)]">
                <div class="p-6 sm:p-8 rounded-2xl bg-[rgb(239,246,255)]">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-1 rounded-2xl shadow
                        bg-gradient-to-br from-[rgb(219,234,254)]
                                           via-[rgb(59,130,246)]
                                           to-[rgb(30,64,175)]">
                <div class="p-6 sm:p-8 rounded-2xl bg-[rgb(239,246,255)]">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Delete User -->
            <div class="p-1 rounded-2xl shadow
                        bg-gradient-to-br from-[rgb(254,226,226)]
                                           via-[rgb(248,113,113)]
                                           to-[rgb(153,27,27)]">
                <div class="p-6 sm:p-8 rounded-2xl bg-[rgb(254,242,242)]">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
