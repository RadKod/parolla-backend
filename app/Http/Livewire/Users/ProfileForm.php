<?php

namespace App\Http\Livewire\Users;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class ProfileForm extends Component
{
    /**
     * @var array current state of the form.
     */
    public array $state = [];

    /**
     * Mounts the component.
     *
     * @return void
     */
    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->state = $user->withoutRelations()->toArray();
    }

    /**
     * Renders the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('users.profile-form');
    }

    /**
     * Updates profile information associated with user.
     *
     * TODO: pass Action class attribute on DI side.
     *
     * @param UpdateUserProfileInformation $updater
     * @return void
     * @throws ValidationException
     */
    public function updateProfileInformation(UpdateUserProfileInformation $updater): void
    {
        $this->resetErrorBag();

        $updater->update(auth()->user(), $this->state);

        session()->flash('status', 'Profile successfully updated');
    }
}
