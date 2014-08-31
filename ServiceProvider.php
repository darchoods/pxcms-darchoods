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
        $this->app->bind(
            'Cysha\Modules\Darchoods\Repositories\Irc\User\RepositoryInterface',
            'Cysha\Modules\Darchoods\Repositories\Irc\User\DbRepository'
        );

        $this->app->bind(
            'Cysha\Modules\Darchoods\Repositories\Irc\Channel\RepositoryInterface',
            'Cysha\Modules\Darchoods\Repositories\Irc\Channel\DbRepository'
        );

        $this->app->bind(
            'Cysha\Modules\Darchoods\Repositories\Irc\Server\RepositoryInterface',
            'Cysha\Modules\Darchoods\Repositories\Irc\Server\DbRepository'
        );

        $this->app->bind(
            'Cysha\Modules\Darchoods\Repositories\Irc\Stat\RepositoryInterface',
            'Cysha\Modules\Darchoods\Repositories\Irc\Stat\DbRepository'
        );
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
