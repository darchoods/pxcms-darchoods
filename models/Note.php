<?php namespace Cysha\Modules\Darchoods\Models;

use Cysha\Modules\Core\Models\BaseModel;
use Config;

class Note extends BaseModel
{
    use \Cysha\Modules\Core\Traits\SelfValidationTrait,
        \Venturecraft\Revisionable\RevisionableTrait{
        \Cysha\Modules\Core\Traits\SelfValidationTrait::boot as validationBoot;
        \Venturecraft\Revisionable\RevisionableTrait::boot as revisionableBoot;
    }

    protected $revisionEnabled = true;

    protected static $messages;
    protected $fillable = ['author_id', 'title', 'content', 'created_at', 'updated_at'];
    protected $identifiableName = 'name';

    public static function boot()
    {
        static::validationBoot();
        static::revisionableBoot();
    }

    public function author()
    {
        $authModel = Config::get('auth.model');
        return $this->belongsTo($authModel);
    }

    public function getContentAttribute($value)
    {
        return \Markdown::parse($value);
    }


    public function transform()
    {
        return [
            'id'       => (int)$this->id,
            'author'   => (array) $this->author->transform(),
            'title'    => (string) $this->title,
            'content'  => (string) $this->content,

            'created'  => date_array($this->created_at),
            'updated'  => date_array($this->updated_at),
        ];
    }
}
