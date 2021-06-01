<?php

namespace Laravel\Jetstream\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\ConfirmEnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateTwoFactorAuthenticationSecret;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Features;
use Laravel\Jetstream\ConfirmsPasswords;
use Livewire\Component;

class TwoFactorAuthenticationForm extends Component
{
    use ConfirmsPasswords;

    /**
     * Indicates if two factor authentication QR code is being displayed.
     *
     * @var bool
     */
    public $showingQrCode = false;

    /**
     * Indicates if two factor authentication recovery codes are being displayed.
     *
     * @var bool
     */
    public $showingRecoveryCodes = false;

    /**
     * Code to confirm activation of two-factor authentication
     * @var string
     */
    public $confirmationCode = null;

    /**
     * Generate two factor authentication secret for user.
     *
     * @param  \Laravel\Fortify\Actions\GenerateTwoFactorAuthenticationSecret  $generate
     * @return void
     */
    public function generateTwoFactorAuthenticationSecret(GenerateTwoFactorAuthenticationSecret $generate)
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $generate(Auth::user());

        $this->showingQrCode = true;
        $this->showingRecoveryCodes = true;
    }

    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Laravel\Fortify\Actions\ConfirmEnableTwoFactorAuthentication  $enable
     * @return void
     */
    public function confirmEnableTwoFactorAuthentication(ConfirmEnableTwoFactorAuthentication $enable)
    {
        $enabled = $enable(Auth::user(), $this->confirmationCode);

        if (!$enabled) {
            $this->addError('confirmationCode', __('The provided two factor authentication code was invalid.'));
            return;
        }

        $this->hideSetup();
    }

    /**
     * Display the user's recovery codes.
     *
     * @return void
     */
    public function showRecoveryCodes()
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $this->showingRecoveryCodes = true;
    }

    /**
     * Generate new recovery codes for the user.
     *
     * @param  \Laravel\Fortify\Actions\GenerateNewRecoveryCodes  $generate
     * @return void
     */
    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate)
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $generate(Auth::user());

        $this->showingRecoveryCodes = true;
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param  \Laravel\Fortify\Actions\DisableTwoFactorAuthentication  $disable
     * @return void
     */
    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable)
    {
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->ensurePasswordIsConfirmed();
        }

        $this->hideSetup();
        $disable(Auth::user());
    }

    protected function hideSetup()
    {
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;
        $this->confirmationCode = null;
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Determine if two-factor authentication is pending configuration.
     *
     * @return bool
     */
    public function getSetupProperty()
    {
        return ! empty($this->user->two_factor_secret) && !$this->user->is_two_factor_enabled;
    }

    /**
     * Determine if two factor authentication is enabled.
     *
     * @return bool
     */
    public function getEnabledProperty()
    {
        return ! empty($this->user->two_factor_secret) && $this->user->is_two_factor_enabled;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('profile.two-factor-authentication-form');
    }
}
