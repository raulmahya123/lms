<section
  class="rounded-2xl p-1 shadow
         bg-gradient-to-br from-white via-[rgb(254,226,226)/.5] to-white">
  <div class="rounded-2xl p-6 bg-white border border-[rgb(229,231,235)] space-y-6">
    <header class="border-b border-[rgb(229,231,235)] pb-4">
      <h2 class="text-xl font-semibold text-[rgb(153,27,27)]">
        {{ __('Delete Account') }}
      </h2>
      <p class="mt-1 text-sm text-[rgb(71,85,105)]">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
      </p>
    </header>

    <!-- Trigger button -->
    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center px-5 py-2.5 rounded-xl text-white font-medium
                   bg-[rgb(220,38,38)] hover:bg-[rgb(185,28,28)]
                   shadow-sm hover:shadow-md
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[rgb(239,68,68)]
                   transition">
      {{ __('Delete Account') }}
    </button>

    <!-- Modal -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
      <form method="post" action="{{ route('profile.destroy') }}"
            class="p-6 space-y-5"
            x-data="{ showPw:false, confirmText:'', pw:'' }">
        @csrf
        @method('delete')

        <h2 class="text-lg font-semibold text-[rgb(153,27,27)]">
          {{ __('Are you sure you want to delete your account?') }}
        </h2>

        <div class="rounded-lg border border-[rgb(254,202,202)] bg-[rgb(254,242,242)] p-3">
          <p class="text-sm text-[rgb(107,33,33)] leading-relaxed">
            {{ __('This action is permanent. All resources and data will be removed and cannot be recovered.') }}
          </p>
        </div>

        <!-- Type to confirm -->
        <div>
          <label class="block text-sm font-medium text-[rgb(124,45,18)]">
            {{ __('Type DELETE to confirm') }}
          </label>
          <input x-model="confirmText" type="text" placeholder="DELETE"
                 class="mt-2 block w-full rounded-xl
                        border border-[rgb(209,213,219)] bg-white
                        placeholder:text-[rgb(148,163,184)]
                        focus:border-[rgb(239,68,68)] focus:ring focus:ring-[rgba(239,68,68,0.2)]
                        transition">
        </div>

        <!-- Password input -->
        <div>
          <label for="password" class="block text-sm font-medium text-[rgb(124,45,18)]">
            {{ __('Password') }}
          </label>
          <div class="mt-2 relative">
            <input id="password" name="password" :type="showPw ? 'text' : 'password'"
                   x-model="pw"
                   placeholder="{{ __('Your password') }}"
                   class="block w-full rounded-xl
                          border border-[rgb(209,213,219)] bg-white
                          placeholder:text-[rgb(148,163,184)]
                          focus:border-[rgb(239,68,68)] focus:ring focus:ring-[rgba(239,68,68,0.2)]
                          transition pr-12">
            <button type="button" @click="showPw = !showPw"
                    class="absolute inset-y-0 right-0 px-3 text-[rgb(100,116,139)] hover:text-[rgb(30,41,59)]">
              <span x-text="showPw ? 'Hide' : 'Show'"></span>
            </button>
          </div>
          <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
        </div>

        <!-- Actions -->
        <div class="mt-4 flex justify-end gap-3">
          <button type="button" x-on:click="$dispatch('close')"
                  class="px-4 py-2.5 rounded-xl border text-[rgb(71,85,105)]
                         border-[rgb(209,213,219)] bg-white hover:bg-[rgb(248,250,252)]
                         transition">
            {{ __('Cancel') }}
          </button>

          <button type="submit"
                  :disabled="confirmText !== 'DELETE' || pw.length === 0"
                  :class="(confirmText !== 'DELETE' || pw.length === 0) ? 'opacity-60 cursor-not-allowed' : ''"
                  class="px-5 py-2.5 rounded-xl text-white font-medium
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
