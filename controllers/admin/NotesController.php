<?php namespace Cysha\Modules\Darchoods\Controllers\Admin;

use Cysha\Modules\Darchoods\Helpers\IRC as IRC;
use Cysha\Modules\Core\Models\DBConfig;
use URL;

class NotesController extends BaseAdminController
{
    use \Cysha\Modules\Admin\Traits\DataTableTrait;

    public function __construct()
    {
        parent::__construct();

        $this->objTheme->setTitle('<i class="glyphicon glyphicon-list-alt"></i> Notes Manager');
        $this->objTheme->breadcrumb()->add('Notes Manager', URL::route('admin.notes.index'));
        $this->assets();

        $this->setActions([
            'header' => [
                [
                    'btn-text'  => 'Add Note',
                    'btn-link'  => URL::Route('admin.notes.add'),
                    'btn-class' => 'btn btn-info btn-labeled',
                    'btn-icon'  => 'fa fa-plus'
                ],
            ],
        ]);

        $this->setTableOptions([
            'filtering'     => false,
            'pagination'    => true,
            'sorting'       => true,
            'sort_column'   => 'id',
            'source'        => URL::route('admin.notes.ajax'),
            'collection'    => function () {
                return \Cysha\Modules\Darchoods\Models\Note::with(['author'])->get();
            },
        ]);

        $this->setTableColumns([
            'id' => [
                'th'        => 'ID',
                'tr'        => function ($model) {
                    return $model->id;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '3%',
            ],
            'author' => [
                'th'        => 'Author',
                'tr'        => function ($model) {
                    return $model->author->name;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '10%',
            ],
            'title' => [
                'th'        => 'Title',
                'tr'        => function ($model) {
                    return $model->title;
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '10%',
            ],
            'title' => [
                'th'        => 'Title',
                'tr'        => function ($model) {
                    return \Str::limit($model->content, 100);
                },
                'sorting'   => true,
                'filtering' => true,
                'width'     => '10%',
            ],
            'created_at' => [
                'th'        => 'Date Created',
                'tr'        => function ($model) {
                    return date_carbon($model->created_at, 'd/m/Y H:i:s');
                },
                'th-class'  => 'hidden-xs hidden-sm',
                'tr-class'  => 'hidden-xs hidden-sm',
                'width'     => '15%',
            ],
            'actions' => [
                'th' => 'Actions',
                'tr' => function ($model) {
                    return [[
                        'btn-text'  => 'Edit',
                        'btn-link'  => ( Auth::user()->can('admin.news.edit') ? sprintf('/admin/news/%d/edit', $model->id) : '#' ),
                        'btn-class' => ( Auth::user()->can('admin.news.edit') ? 'btn btn-warning btn-sm btn-labeled' : 'btn btn-warning btn-sm btn-labeled disabled' ),
                        'btn-icon'  => 'fa fa-pencil'
                    ],[
                        'btn-text'  => 'View',
                        'btn-link'  => ( Auth::user()->can('admin.news.edit') ? sprintf('/admin/news/%d/edit', $model->id) : '#' ),
                        'btn-class' => ( Auth::user()->can('admin.news.edit') ? 'btn btn-default btn-sm btn-labeled' : 'btn btn-default btn-sm btn-labeled disabled' ),
                        'btn-icon'  => 'fa fa-file'
                    ]];
                },
            ]
        ]);
    }

}
