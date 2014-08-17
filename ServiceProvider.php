<?php namespace Cysha\Modules\Darchoods;

use Illuminate\Foundation\AliasLoader;
use Cysha\Modules\Core\BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        \Config::set('auth.model', 'Cysha\Modules\Darchoods\Models\User');

        //$this->registerOtherPackages();
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
