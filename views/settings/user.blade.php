<div class="row">
    <div class="col-md-3">@include('darchoods::settings.nav')</div>
    <div class="col-md-9">
        {{ Former::horizontal_open() }}

        {{ Former::text('first_name')->label('First Name') }}
        {{ Former::text('last_name')->label('Last Name') }}
        {{ Former::select('use_nick')->options([-1 => 'First LastName']+$user->nicks)->label('Use Nick')->help('This nick will be what you will see across the site.') }}


        <button class="btn-labeled btn btn-success pull-right" type="submit">
            <span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span> Save Settings
        </button>

        {{ Former::close() }}
    </div>
</div>
