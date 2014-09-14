<?php namespace Cysha\Modules\Darchoods\Controllers\Admin\NoteManager;

use Cysha\Modules\Darchoods as Darchoods;
use Former;
use Input;
use Redirect;

class AddNoteController extends BaseNoteManagerController
{

    public function getAdd()
    {
        return $this->setView('notes.admin.form');
    }

    public function postAdd()
    {
        $input = Input::except('_token');

        $input['author_id'] = \Auth::user()->id;

        $titleCheck = Darchoods\Models\Note::whereTitle(Input::get('title'))->get()->first();
        if ($titleCheck !== null) {
            return Redirect::route('admin.notes.add')->withInput()->withError('Error, title already exists, pick another.');
        }

        $obj = new Darchoods\Models\Note;
        $obj->fill($input);
        if (!$obj->save()) {
            return Redirect::route('admin.notes.add')->withErrors($obj->errors());
        }

        return Redirect::route('admin.notes.view', $obj->id)->withInfo('Note Added');
    }
}
