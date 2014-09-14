<div class="col-md-6">
    {{ Former::horizontal_open() }}
        {{ Former::text('title')->required() }}

        {{ Former::textarea('content')->rows(20)->inlineHelp('<a href="http://daringfireball.net/projects/markdown/syntax" target="_blank"><i class="glyphicon glyphicon-new-window"></i> Markdown Docs</a>')->required() }}

        <button class="btn-labeled btn btn-success pull-right" type="submit">
            <span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span> Save
        </button>

    {{ Former::close() }}
</div>
