<x-jet-action-section>
    <x-slot name="title">
        {{ __('Two Factor Authentication') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Add additional security to your account using two factor authentication.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-gray-900">
            @if ($this->enabled)
                {{ __('You have enabled two factor authentication.') }}
            @else
                {{ __('You have not enabled two factor authentication.') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600">
            <p>
                {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
            </p>
        </div>

        @if ($showingRecoveryCodes)
            <div class="mt-4 max-w-xl text-sm text-gray-600">
                <p class="font-semibold">
                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                </p>
            </div>

            <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
                @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                    <div>{{ $code }}</div>
                @endforeach
            </div>
        @endif

        @if ($this->setup || $showingQrCode)
            <div class="mt-4 max-w-xl text-sm text-gray-600">
                <p class="font-semibold">
                    {{ __('Scan the following QR code using your phone\'s authenticator application to setup two factor authentication.') }}
                </p>
            </div>

            <div class="mt-4 dark:p-4 dark:w-56 dark:bg-white">
                {!! $this->user->twoFactorQrCodeSvg() !!}
            </div>
        @endif

        @if ($this->setup)
            <div class="mt-4 max-w-xl text-sm text-gray-600">
                <div class="col-span-6 sm:col-span-4">
                    <x-jet-label for="confirmationCode" value="{{ __('After configuring the authenticator application, enter the code to validate the two-factor authentication.') }}" />
                    <x-jet-input id="confirmationCode" type="text" class="mt-1 block w-full" wire:model.defer="confirmationCode" />
                    <x-jet-input-error for="confirmationCode" class="mt-2" />
                </div>
            </div>
        @endif

        <div class="mt-5">
            @if (! $this->setup && !$this->enabled)
                <x-jet-confirms-password wire:then="generateTwoFactorAuthenticationSecret">
                    <x-jet-button type="button" wire:loading.attr="disabled">
                        {{ __('Enable') }}
                    </x-jet-button>
                </x-jet-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-jet-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-jet-secondary-button class="mr-3">
                            {{ __('Regenerate Recovery Codes') }}
                        </x-jet-secondary-button>
                    </x-jet-confirms-password>
                @else
                    <x-jet-confirms-password wire:then="showRecoveryCodes">
                        <x-jet-secondary-button class="mr-3">
                            {{ __('Show Recovery Codes') }}
                        </x-jet-secondary-button>
                    </x-jet-confirms-password>
                @endif

                @if($this->enabled)
                    <x-jet-confirms-password wire:then="disableTwoFactorAuthentication">
                        <x-jet-danger-button wire:loading.attr="disabled">
                            {{ __('Disable') }}
                        </x-jet-danger-button>
                    </x-jet-confirms-password>
                @else
                    <x-jet-button wire:click="confirmEnableTwoFactorAuthentication" wire:loading.attr="disabled">
                        {{ __('Confirm') }}
                    </x-jet-button>
                @endif
            @endif
        </div>
    </x-slot>
</x-jet-action-section>
