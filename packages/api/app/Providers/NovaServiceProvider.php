<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Nova\AdminUser;
use App\Nova\ClientUser;
use App\Nova\Conveyancer;
use App\Nova\ConveyancerUser;
use App\Nova\Dashboards\Main;
use App\Nova\Form;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::withoutThemeSwitcher();

        Nova::footer(function ($request) {
            return Blade::render('
                <p class="text-center">© {!! $year !!} ProConvey</p>
            ', [
                'year' => date('Y'),
            ]);
        });

        Nova::createUserUsing(function (Command $command) {
            return [
                $command->choice('Role', collect(UserRole::getValues())->map(fn ($s) => Str::title($s))->toArray()),
                $command->ask('First Name'),
                $command->ask('Last Name'),
                $command->ask('Email'),
                $command->secret('Password'),
            ];
        }, function (string $role, string $firstName, string $lastName, string $email, string $password) {
            (new User)
                ->forceFill([
                    'role' => Str::lower($role),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => $password,
                ])
                ->save();
        });

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)
                    ->icon('chart-bar'),

                MenuSection::make('Forms')
                    ->resource(Form::class)
                    ->icon('clipboard-list'),

                MenuSection::make('Conveyancing Firms')
                    ->resource(Conveyancer::class)
                    ->icon('office-building'),

                MenuSection::make('Users', [
                    MenuItem::resource(AdminUser::class),
                    MenuItem::resource(ClientUser::class),
                    MenuItem::resource(ConveyancerUser::class),
                ])
                    ->icon('user'),
            ];
        });

        Nova::report(function ($exception) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($exception);
            }
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return $user->role === UserRole::Admin;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
