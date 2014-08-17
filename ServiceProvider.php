<?php namespace Cysha\Modules\Darchoods;

use Illuminate\Foundation\AliasLoader;
use Cysha\Modules\Core\BaseServiceProvider;
use Cysha\Modules\Darchoods\Commands\InstallCommand;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        \Config::set('auth.model', 'Cysha\Modules\Darchoods\Models\User');

        //$this->registerOtherPackages();
    }

    private function registerInstallCommand()
    {
        $this->app['cms.modules.darchoods:install'] = $this->app->share(function () {
            return new InstallCommand($this->app);
        });
        $this->commands('cms.modules.auth:install');
    }

    private function registerOtherPackages()
    {
        $serviceProviders = [
            'Thomaswelton\LaravelGravatar\LaravelGravatarServiceProvider',
        ];

        foreach ($serviceProviders as $sp) {
            $this->app->register($sp);
        }

        $aliases = [
            'Gravatar' => 'Thomaswelton\LaravelGravatar\Facades\Gravatar',
        ];

        foreach ($aliases as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }
    }

}
