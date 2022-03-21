<?php

namespace App\Http\Livewire\Users;

use App\Actions\Fortify\UpdateUserPassword;
use Livewire\Component;

class PasswordChangeForm extends Component
{
    public $state = [
        'current_password' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    public function render()
    {
        return view('livewire.users.password-change-form');
    }

    public function updateProfileInformation(UpdateUserPassword $updater): void
    {
        $this->resetErrorBag();

        $updater->update(auth()->user(), $this->state);

        $this->state = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        session()->flash('status', 'Password successfully changed');
    }

}
