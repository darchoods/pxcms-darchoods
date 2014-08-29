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
        $this->registerIrcUserRepository();
        $this->registerIrcChannelRepository();
        $this->registerIrcServerRepository();
    }


    public function registerIrcUserRepository()
    {
        $this->app->bind('Cysha\Modules\Darchoods\Repositories\Irc\User\RepositoryInterface', function ($app) {
            return new Module\Repositories\Irc\User\DbRepository(new Module\Models\Irc\User);
        });
    }

    public function registerIrcChannelRepository()
    {
        $this->app->bind('Cysha\Modules\Darchoods\Repositories\Irc\Channel\RepositoryInterface', function ($app) {
            return new Module\Repositories\Irc\Channel\DbRepository(new Module\Models\Irc\Channel);
        });
    }

    public function registerIrcServerRepository()
    {
        $this->app->bind('Cysha\Modules\Darchoods\Repositories\Irc\Server\RepositoryInterface', function ($app) {
            return new Module\Repositories\Irc\Server\DbRepository(new Module\Models\Irc\Server);
        });
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
