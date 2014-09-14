<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Pages;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController;
use Cysha\Modules\News as News;

class NewsController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getNews()
    {
        $posts = News\Models\News::getCurrent(5);

        return $this->setView('news.homepage', [
            'posts' => $posts
        ], 'module:news');
    }

    public function getNewsById(News\Models\News $objNews)
    {

        return $this->setView('news._row', [
            'post' => $objNews->transform()
        ], 'module:news');
    }

}
