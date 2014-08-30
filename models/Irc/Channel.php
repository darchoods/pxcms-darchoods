<?php namespace Cysha\Modules\Darchoods\Models\Irc;

use Cysha\Modules\Darchoods\Helpers\Irc as Irc;

class Channel extends BaseModel
{
    public $table = 'chan';

    public function getTopicAttribute($value)
    {
        $colorize = new Irc\MircColorParser();
        $value = e($value);
        $value = $colorize->colorize($value);
        $value = denora_colorconvert($value);

        return $value;
    }

    public function getModesAttribute()
    {
        $checkModes = chan_modes($this->attributes);
        if (strstr($checkModes, ' ')) {
            $checkModes = explode(' ', $checkModes);
            $checkModes = $checkModes[0];
        }
        if ($checkModes == '+') {
            $checkModes = '';
        }

        return $checkModes;
    }

    public function transform()
    {

        return [
            'name'  => (string) $this->channel,
            'modes' => (string) $this->modes,
            'topic' => [
                'raw'    => (string) $this->getOriginal('topic'),
                'html'   => (string) $this->topic,
                'author' => (string) $this->topicauthor,
                'time'   => strtotime($this->topictime),
            ],
            'stats' => [
                'current_users'  => (int) $this->currentusers,
                'peak_users'     => (int) $this->maxusers,
                'peak_user_time' => (int) $this->maxusertime,
            ],
        ];
    }
}
