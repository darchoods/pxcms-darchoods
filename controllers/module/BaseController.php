<?php namespace Cysha\Modules\Darchoods\Controllers\Module;

use Cysha\Modules\Core\Controllers\BaseModuleController as BMC;
use URL;

class BaseController extends BMC
{
    public $layout = 'cols-2-right';

    public function __construct()
    {
        parent::__construct();
        $this->objTheme->set('mode', 'basic');
    }

    public function setDecorativeMode()
    {
        if (!is_object($this->objTheme)) {
            return false;
        }
        $this->objTheme->set('mode', 'decorative');
    }
}
