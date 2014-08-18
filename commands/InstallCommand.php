<?php namespace Cysha\Modules\Darchoods\Commands;

use Cysha\Modules\Core\Commands\BaseCommand;

class InstallCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cms.modules.darchoods:install';

    /**
     * The Readable Module Name.
     *
     * @var string
     */
    protected $readableName = 'Darchoods Module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the Darchoods Module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $packages = array(
        );

        $this->install($packages);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
        );
    }

}
