<?php namespace Cysha\Modules\Darchoods\Controllers\Admin\NoteManager;

use Cysha\Modules\Darchoods as Darchoods;
use URL;

class ViewNoteController extends BaseNoteManagerController
{

    public function getView(Darchoods\Models\Note $obj)
    {
        $this->objTheme->setTitle('Notes <small>> '.$obj->title.'</small>');
        $this->setActions([
            'header' => [
                [
                    'btn-text'  => 'Back to Notes',
                    'btn-link'  => URL::Route('admin.notes.index'),
                    'btn-class' => 'btn btn-default btn-labeled',
                    'btn-icon'  => 'glyphicon glyphicon-book'
                ],
                [
                    'btn-text'  => 'Edit Note',
                    'btn-link'  => 'edit',
                    'btn-class' => 'btn btn-warning btn-labeled',
                    'btn-icon'  => 'fa fa-pencil'
                ],
            ],
        ]);

        return $this->setView('notes._row', [
            'post' => $obj->transform()
        ]);
    }

}
