<section
  class="rounded-2xl p-1 shadow
         bg-gradient-to-br from-white via-[rgb(219,234,254)] to-white">
  <!-- inner panel -->
  <div class="rounded-2xl p-6 bg-white border border-[rgb(229,231,235)]">
    <header class="mb-6 border-b pb-4">
      <h2 class="text-xl font-semibold text-[rgb(30,64,175)]">
        {{ __('Profile Information') }}
      </h2>
      <p class="mt-1 text-sm text-[rgb(71,85,105)]">
        {{ __("Update your account's profile information and email address.") }}
      </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- NAME -->
        <div>
            <label for="name" class="block text-sm font-medium text-[rgb(30,64,175)]">
                {{ __('Name') }}
            </label>
            <input id="name" name="name" type="text"
                   class="mt-2 block w-full rounded-xl
                          border border-[rgb(209,213,219)]
                          bg-white text-[rgb(30,41,59)]
                          focus:border-[rgb(37,99,235)] focus:ring focus:ring-[rgba(37,99,235,0.2)]
                          transition"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- EMAIL -->
        <div>
            <label for="email" class="block text-sm font-medium text-[rgb(30,64,175)]">
                {{ __('Email') }}
            </label>
            <input id="email" name="email" type="email"
                   class="mt-2 block w-full rounded-xl
                          border border-[rgb(209,213,219)]
                          bg-white text-[rgb(30,41,59)]
                          focus:border-[rgb(37,99,235)] focus:ring focus:ring-[rgba(37,99,235,0.2)]
                          transition"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-lg border border-yellow-300 bg-yellow-50">
                    <p class="text-sm text-yellow-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification"
                                class="ml-2 underline text-[rgb(37,99,235)] hover:text-[rgb(29,78,216)] font-medium">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- ACTIONS -->
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="inline-flex items-center px-5 py-2.5 rounded-xl text-white font-medium
                           bg-[rgb(37,99,235)] hover:bg-[rgb(29,78,216)]
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[rgb(59,130,246)]
                           shadow-sm transition">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-[rgb(5,150,105)]">
                   {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
  </div>
</section>
