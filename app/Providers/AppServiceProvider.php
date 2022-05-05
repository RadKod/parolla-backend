<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootCollate();
        $this->bootSchema();
        $this->setupFortify();
        $this->setupJetstream();
    }

    /**
     * Set locale of the collation.
     *
     * @return void
     */
    protected function bootCollate()
    {
        setlocale(LC_COLLATE, config('app.locale_collate'));
    }

    /**
     * Boots up the Schema and migration related stuff.
     *
     * @return void
     */
    protected function bootSchema()
    {
        Schema::defaultstringLength(191);
    }

    /**
     * Setup fortify package for authentication.
     *
     * @return void
     */
    protected function setupFortify()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    /**
     * Setup jetstream package
     *
     * @return void
     */
    protected function setupJetstream()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);

        Jetstream::deleteUsersUsing(DeleteUser::class);
    }
}
