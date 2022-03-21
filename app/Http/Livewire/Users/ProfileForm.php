<?php

namespace App\Http\Livewire\Users;

use App\Actions\Fortify\UpdateUserProfileInformation;
use Livewire\Component;

class ProfileForm extends Component
{
    public $state = [];

    public function mount(): void
    {
        $this->state = auth()->user()->withoutRelations()->toArray();
    }

    public function render()
    {
        return view('livewire.users.profile-form');
    }

    public function updateProfileInformation(UpdateUserProfileInformation $updater): void
    {
        $this->resetErrorBag();
        $updater->update(auth()->user(), $this->state);

        session()->flash('status', 'Profile successfully updated');
    }
}
