<?php

namespace App\Http\Livewire\Users;

use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class PasswordChangeForm extends Component
{
    /**
     * @var array|string[] current state of the form
     */
    public array $state = [
        'current_password' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    /**
     * Renders component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('users.password-change-form');
    }

    /**
     * Updates profile information with associated user.
     *
     * @param UpdateUserPassword $updater
     * @return void
     * @throws ValidationException
     */
    public function updateProfileInformation(UpdateUserPassword $updater): void
    {
        $this->resetErrorBag();

        $updater->update(auth()->user(), $this->state);

        // Resets current state
        $this->state = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        session()->flash('status', 'Password successfully changed');
    }

}
