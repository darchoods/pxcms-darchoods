<?php namespace Cysha\Modules\Darchoods;

use Illuminate\Foundation\AliasLoader;
use Cysha\Modules\Core\BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        //$this->registerOtherPackages();
    }

    private function registerOtherPackages()
    {
        $serviceProviders = [
        ];

        foreach ($serviceProviders as $sp) {
            $this->app->register($sp);
        }

        $aliases = [
        ];

        foreach ($aliases as $alias => $class) {
            AliasLoader::getInstance()->alias($alias, $class);
        }
    }

}
