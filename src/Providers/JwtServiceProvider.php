<?php

/*
 * This file is part of Aikrof JWT-Auth.
 */

namespace Aikrof\JwtAuth\Providers;

use Illuminate\Support\ServiceProvider;
use Aikrof\JwtAuth\Console\JwtGenerateCommand;

class JwtServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publish();
        $this->registerAliase();

        $this->registerCommand();
        $this->commands('aikrof.secret.command');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->register(
            'Aikrof\JwtAuth\Providers\AuthServiceProvider'
        );

        $this->loadMigrationsFrom(
            realpath(__DIR__ . '/../../database/migrations')
        );
    }

    protected function publish()
    {
        $path = realpath(__DIR__.'/../../config/jwt.php');

        $this->publishes([
            $path => config_path('jwt.php')
        ]);
        $this->mergeConfigFrom($path, 'jwt');
    }

    protected function registerAliase()
    {
        $this->app->alias('aikrof.jwtAuth.jwtConfig',JwtConfig::class);
    }

    protected function registerCommand()
    {
        $this->app->singleton('aikrof.secret.command', function(){
            return new JwtGenerateCommand;
        });
    }

}
