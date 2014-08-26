<?php namespace Cysha\Modules\Darchoods\Controllers\Module;

use Cysha\Modules\Core\Controllers\BaseModuleController as CoreController;
use URL;

class BaseController extends CoreController
{

    public function __construct()
    {
        parent::__construct();
        $this->objTheme->set('mode', 'basic');
    }

    public function setDecorativeMode()
    {
        $this->objTheme->set('mode', 'decorative');
    }
}
