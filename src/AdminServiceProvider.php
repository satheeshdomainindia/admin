<?php

namespace laravelzone\admin;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use laravelzone\admin\Console\Commands\RoleCmd;
use laravelzone\admin\Console\Commands\SeedCmd;
use laravelzone\admin\Exception\MultiAuthHandler;
use laravelzone\admin\Console\Commands\RemoveAdminCommand;
use laravelzone\admin\Console\Commands\InstallAdminCommand;
use laravelzone\admin\Console\Commands\MakeMultiAuthCommand;
use laravelzone\admin\Console\Commands\RollbackMultiAuthCommand;
use laravelzone\admin\Http\Middleware\redirectIfAuthenticatedAdmin;
use laravelzone\admin\Http\Middleware\redirectIfNotWithRoleOfAdmin;


class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->canHaveAdminBackend()) {       
        $this->loadBladeSyntax();
        $this->loadAdminCommands();
        }
        $this->loadCommands();
        $this->publisheThings();
    }

    public function register()
    {
        if ($this->canHaveAdminBackend()) {
            $this->loadMiddleware();
            $this->registerExceptionHandler();
        }
    }
   
    
   
    protected function loadMiddleware()
    {
        app('router')->aliasMiddleware('admin', redirectIfAuthenticatedAdmin::class);
        app('router')->aliasMiddleware('role', redirectIfNotWithRoleOfAdmin::class);
    }

    protected function registerExceptionHandler()
    {
        \App::singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            MultiAuthHandler::class
        );
    }

   
    protected function publisheThings()
    {
        $this->publishes([
            __DIR__.'/../app/views' => resource_path('views/admin'),
        ], 'admin:views');
        $this->publishes([
            __DIR__.'/../app/assets' => public_path('assests'),
        ], 'admin:assets');
        $this->publishes([
            __DIR__.'/../app/Controllers' => app_path ('Http/Controllers/Admin'),
        ], 'admin:controllers');
        $this->publishes([
            __DIR__.'/../app/database/migrations/' => database_path('migrations'),
        ], 'admin:migrations');
        $this->publishes([
            __DIR__.'/../app/database/factories' => database_path('factories'),
        ], 'admin:factories');
        $this->publishes([
            __DIR__.'/../app/config/admin.php' => config_path('admin.php'),
        ], 'admin:config');
        $this->publishes([
            __DIR__.'/../app/Model/Admin.php' => app_path('Admin.php'),
            __DIR__.'/../app/Model/Role.php' => app_path('Role.php'),
        ], 'admin:models');
        $prefix = config('admin.prefix', 'admin');
        $this->publishes([
            __DIR__.'/../app/routes/admin.php' => base_path("routes/{$prefix}.php"),
        ], 'admin:routes');
     
    }

    protected function loadBladeSyntax()
    {
        Blade::if('admin', function ($role) {
            if (! auth('admin')->check()) {
                return  false;
            }
            $role = explode(',', $role);
            $role[] = 'super';
            $roles = auth('admin')->user()->/* @scrutinizer ignore-call */ roles()->pluck('name');
            $match = count(array_intersect($role, $roles->toArray()));

            return (bool) $match;
        });
    }

    protected function loadAdminCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedCmd::class,
                RoleCmd::class,
            ]);
        }
    }

    protected function loadCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeMultiAuthCommand::class,
                RollbackMultiAuthCommand::class,
                InstallAdminCommand::class,
                RemoveAdminCommand::class,
            ]);
        }
    }

    protected function canHaveAdminBackend()
    {
        return config('admin.admin_active', true);
    }
}
