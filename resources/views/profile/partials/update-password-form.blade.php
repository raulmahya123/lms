<section
  class="rounded-2xl p-1 shadow
         bg-gradient-to-br from-[rgb(219,234,254)] via-[rgb(59,130,246)] to-[rgb(30,64,175)]">
  <!-- inner panel putih -->
  <div class="rounded-2xl p-6 bg-white">
    <header>
      <h2 class="text-xl font-semibold text-[rgb(30,64,175)]">
        {{ __('Update Password') }}
      </h2>
      <p class="mt-1 text-sm text-[rgb(71,85,105)]">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
      </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
      @csrf
      @method('put')

      <!-- Current Password -->
      <div>
        <label for="update_password_current_password"
               class="block text-sm font-medium text-[rgb(30,64,175)]">
          {{ __('Current Password') }}
        </label>
        <input id="update_password_current_password" name="current_password" type="password"
               class="mt-2 block w-full rounded-xl
                      bg-white border border-[rgb(191,219,254)]
                      placeholder:text-[rgb(148,163,184)]
                      focus:border-[rgb(37,99,235)]
                      focus:ring focus:ring-[rgba(37,99,235,0.25)]
                      transition"
               autocomplete="current-password" />
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
      </div>

      <!-- New Password -->
      <div>
        <label for="update_password_password"
               class="block text-sm font-medium text-[rgb(30,64,175)]">
          {{ __('New Password') }}
        </label>
        <input id="update_password_password" name="password" type="password"
               class="mt-2 block w-full rounded-xl
                      bg-white border border-[rgb(191,219,254)]
                      placeholder:text-[rgb(148,163,184)]
                      focus:border-[rgb(37,99,235)]
                      focus:ring focus:ring-[rgba(37,99,235,0.25)]
                      transition"
               autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="update_password_password_confirmation"
               class="block text-sm font-medium text-[rgb(30,64,175)]">
          {{ __('Confirm Password') }}
        </label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password"
               class="mt-2 block w-full rounded-xl
                      bg-white border border-[rgb(191,219,254)]
                      placeholder:text-[rgb(148,163,184)]
                      focus:border-[rgb(37,99,235)]
                      focus:ring focus:ring-[rgba(37,99,235,0.25)]
                      transition"
               autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-4">
        <button type="submit"
                class="inline-flex items-center px-5 py-2.5 rounded-xl text-white font-medium
                       bg-[rgb(37,99,235)] hover:bg-[rgb(29,78,216)]
                       shadow hover:shadow-md
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[rgb(59,130,246)]
                       transition">
          {{ __('Save') }}
        </button>

        @if (session('status') === 'password-updated')
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
