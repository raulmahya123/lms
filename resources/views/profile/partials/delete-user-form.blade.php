<section
  class="rounded-2xl p-1 shadow
         bg-gradient-to-br from-[rgb(254,226,226)] via-[rgb(248,113,113)] to-[rgb(153,27,27)]">
  <div class="rounded-2xl p-6 bg-[rgb(254,242,242)] space-y-6">
    <header>
      <h2 class="text-xl font-semibold text-[rgb(153,27,27)]">
          {{ __('Delete Account') }}
      </h2>

      <p class="mt-1 text-sm text-[rgb(107,33,33)]">
          {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
      </p>
    </header>

    <!-- Trigger button -->
    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center px-5 py-2.5 rounded-xl text-white font-medium
                   bg-[rgb(220,38,38)] hover:bg-[rgb(185,28,28)]
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[rgb(239,68,68)]
                   transition">
        {{ __('Delete Account') }}
    </button>

    <!-- Modal -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-4">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-[rgb(153,27,27)]">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="text-sm text-[rgb(107,33,33)]">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <!-- Password input -->
            <div class="mt-4">
                <label for="password" class="sr-only">{{ __('Password') }}</label>
                <input id="password" name="password" type="password"
                       placeholder="{{ __('Password') }}"
                       class="mt-1 block w-3/4 rounded-xl border border-[rgb(254,202,202)]
                              bg-white text-[rgb(30,41,59)]
                              focus:border-[rgb(239,68,68)] focus:ring focus:ring-[rgba(239,68,68,0.25)]
                              transition" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')"
                        class="px-4 py-2.5 rounded-xl border text-[rgb(107,33,33)]
                               border-[rgb(254,202,202)] bg-white hover:bg-[rgb(254,226,226)]
                               transition">
                    {{ __('Cancel') }}
                </button>

                <button type="submit"
                        class="ms-3 px-5 py-2.5 rounded-xl text-white font-medium
                               bg-[rgb(220,38,38)] hover:bg-[rgb(185,28,28)]
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[rgb(239,68,68)]
                               transition">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
  </div>
</section>
