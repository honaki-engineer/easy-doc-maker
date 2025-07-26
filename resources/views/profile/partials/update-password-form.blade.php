<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('profile.update_password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('profile.Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" :value="__('profile.current_password')" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full {{ Auth::user()->isGuest() ? 'cursor-not-allowed' : '' }}" autocomplete="current-password" :disabled="Auth::user()->isGuest()" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('profile.new_password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full {{ Auth::user()->isGuest() ? 'cursor-not-allowed' : '' }}" autocomplete="new-password" :disabled="Auth::user()->isGuest()" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('profile.confirm_password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full {{ Auth::user()->isGuest() ? 'cursor-not-allowed' : '' }}" autocomplete="new-password" :disabled="Auth::user()->isGuest()" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            @if(Auth::user()->isGuest())
                <x-primary-button disabled class="opacity-50 cursor-not-allowed">
                    {{ __('profile.save') }}
                </x-primary-button>
            @else
                <x-primary-button>
                    {{ __('profile.save') }}
                </x-primary-button>
            @endif

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('profile.saved.') }}</p>
            @endif
        </div>
    </form>
</section>
