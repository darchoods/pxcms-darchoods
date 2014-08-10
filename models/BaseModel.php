<?php namespace Cysha\Modules\Pages\Models;

use Cysha\Modules\Core\Models\BaseModel as CoreBaseModel;
use Config;

class BaseModel extends CoreBaseModel
{
    use \Cysha\Modules\Core\Traits\LinkableTrait;

    protected $identifiableName = 'title';

    public function __construct()
    {
        parent::__construct();
        $this->linkableConstructor();
    }

    public function identifiableName()
    {
        return $this->{$this->identifiableName};
    }

}
