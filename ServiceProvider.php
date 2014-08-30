<?php namespace Cysha\Modules\Darchoods;

use Illuminate\Foundation\AliasLoader;
use Cysha\Modules\Core\BaseServiceProvider;
use Cysha\Modules\Darchoods\Commands\InstallCommand;
use Cysha\Modules\Darchoods as Module;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        \Config::set('auth.model', 'Cysha\Modules\Darchoods\Models\User');
        \Config::set('auth::user.redirect_to', 'pxcms.user.dashboard');

        //$this->registerOtherPackages();
        $this->registerRepositories();
        $this->registerViewComposers();
    }


    public function registerRepositories()
    {
        $this->app->bind('Cysha\Modules\Darchoods\Repositories\Irc\User\RepositoryInterface', function ($app) {
            return new Module\Repositories\Irc\User\DbRepository(new Module\Models\Irc\User);
        });

        $this->app->bind('Cysha\Modules\Darchoods\Repositories\Irc\Channel\RepositoryInterface', function ($app) {
            return new Module\Repositories\Irc\Channel\DbRepository(new Module\Models\Irc\Channel);
        });

        $this->app->bind('Cysha\Modules\Darchoods\Repositories\Irc\Server\RepositoryInterface', function ($app) {
            return new Module\Repositories\Irc\Server\DbRepository(new Module\Models\Irc\Server);
        });

        $this->app->bind('Cysha\Modules\Darchoods\Repositories\Irc\Stat\RepositoryInterface', function ($app) {
            return new Module\Repositories\Irc\Stat\DbRepository(new Module\Models\Irc\Stat, new Module\Models\Irc\Maxvalue);
        });
    }

    public function registerViewComposers()
    {
        $this->app->make('view')->composer('theme.*::views/partials.theme.sidebar-*', '\Cysha\Modules\Darchoods\Composers\Sidebar');
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
