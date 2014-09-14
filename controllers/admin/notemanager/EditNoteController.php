<?php namespace Cysha\Modules\Darchoods\Controllers\Admin\NoteManager;

use Cysha\Modules\Darchoods as Darchoods;
use Former;
use Input;
use Redirect;

class EditNoteController extends BaseNoteManagerController
{

    public function getEdit(Darchoods\Models\Note $obj)
    {

        Former::populateField('content', $obj->getOriginal('content'));
        Former::populateField('title', $obj->getOriginal('title'));

        return $this->setView('notes.admin.form');
    }

    public function postEdit(Darchoods\Models\Note $obj)
    {
        $titleCheck = Darchoods\Models\Note::whereTitle(Input::get('title'))->where('id', '<>', $obj->id)->get()->first();
        if ($titleCheck !== null) {
            return Redirect::route('admin.notes.edit', $obj->id)->withInput()->withError('Error, title already exists, pick another.');
        }

        $obj->fill(Input::except('_token'));
        if (!$obj->save()) {
            return Redirect::route('admin.notes.edit', $obj->id)->withErrors($obj->errors());
        }

        return Redirect::route('admin.notes.view', $obj->id)->withInfo('Note Updated');
    }
}
