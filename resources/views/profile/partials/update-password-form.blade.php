<section
  class="rounded-2xl p-1 shadow
         bg-gradient-to-br from-white via-[rgb(219,234,254)/.35] to-white">
  <!-- inner panel putih -->
  <div class="rounded-2xl p-6 bg-white border border-[rgb(229,231,235)]">
    <header class="mb-6 border-b border-[rgb(229,231,235)] pb-4">
      <h2 class="text-xl font-semibold text-[rgb(30,64,175)]">
        {{ __('Update Password') }}
      </h2>
      <p class="mt-1 text-sm text-[rgb(71,85,105)]">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
      </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6" x-data="{ show1:false, show2:false, pw:'', score:0 }"
          x-init="$watch('pw', v => {
            // skor sederhana: panjang + variasi karakter
            let s = 0;
            if(v.length >= 8) s+=30;
            if(v.length >= 12) s+=20;
            if(/[A-Z]/.test(v)) s+=15;
            if(/[a-z]/.test(v)) s+=15;
            if(/\d/.test(v)) s+=10;
            if(/[^A-Za-z0-9]/.test(v)) s+=10;
            score = Math.min(100, s);
          })">
      @csrf
      @method('put')

      <!-- Current Password -->
      <div>
        <label for="update_password_current_password"
               class="block text-sm font-medium text-[rgb(30,64,175)]">
          {{ __('Current Password') }}
        </label>

        <div class="mt-2 relative">
          <input id="update_password_current_password" name="current_password"
                 :type="show1 ? 'text' : 'password'"
                 class="block w-full rounded-xl
                        bg-white border border-[rgb(209,213,219)]
                        placeholder:text-[rgb(148,163,184)]
                        focus:border-[rgb(37,99,235)]
                        focus:ring focus:ring-[rgba(37,99,235,0.2)]
                        transition pr-12" autocomplete="current-password" />
          <button type="button" @click="show1 = !show1"
                  class="absolute inset-y-0 right-0 px-3 text-[rgb(100,116,139)] hover:text-[rgb(30,41,59)]">
            <span x-text="show1 ? 'Hide' : 'Show'"></span>
          </button>
        </div>

        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
      </div>

      <!-- New Password -->
      <div>
        <label for="update_password_password"
               class="block text-sm font-medium text-[rgb(30,64,175)]">
          {{ __('New Password') }}
        </label>

        <div class="mt-2 relative">
          <input id="update_password_password" name="password"
                 :type="show2 ? 'text' : 'password'"
                 x-model="pw"
                 class="block w-full rounded-xl
                        bg-white border border-[rgb(209,213,219)]
                        placeholder:text-[rgb(148,163,184)]
                        focus:border-[rgb(37,99,235)]
                        focus:ring focus:ring-[rgba(37,99,235,0.2)]
                        transition pr-12" autocomplete="new-password" />
          <button type="button" @click="show2 = !show2"
                  class="absolute inset-y-0 right-0 px-3 text-[rgb(100,116,139)] hover:text-[rgb(30,41,59)]">
            <span x-text="show2 ? 'Hide' : 'Show'"></span>
          </button>
        </div>

        <!-- Strength meter -->
        <div class="mt-2">
          <div class="h-2 w-full rounded-full bg-[rgb(241,245,249)] overflow-hidden">
            <div class="h-full"
                 :style="`width:${score}%;`"
                 :class="score<40 ? 'bg-red-400' : (score<70 ? 'bg-yellow-400' : 'bg-emerald-500')"></div>
          </div>
          <p class="mt-1 text-xs text-[rgb(100,116,139)]">
            <span x-text="score<40 ? '{{ __('Weak') }}' : (score<70 ? '{{ __('Medium') }}' : '{{ __('Strong') }}')"></span>
          </p>
        </div>

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
                      bg-white border border-[rgb(209,213,219)]
                      placeholder:text-[rgb(148,163,184)]
                      focus:border-[rgb(37,99,235)]
                      focus:ring focus:ring-[rgba(37,99,235,0.2)]
                      transition" autocomplete="new-password" />
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-4">
        <button type="submit"
                class="inline-flex items-center px-5 py-2.5 rounded-xl text-white font-medium
                       bg-[rgb(37,99,235)] hover:bg-[rgb(29,78,216)]
                       shadow-sm hover:shadow-md
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
