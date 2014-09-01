<table class="table table-bordered table-hover">
    <tr>
        <th>Node Name</th>
        <th>Location</th>
        <th>Current / Peak Users</th>
        <th>Uptime</th>
    </tr>
@foreach ($serverList as $model)
    <tr>
        <td>
            {{ (array_get($model, 'status', false) === true ? '<div class="label label-success">Online</div>' : '<div class="label label-danger">offline</div>') }} {{ array_get($model, 'name') }} :6697
            @if (array_get($model, 'location', null) !== null)
            <span data-toggle="tooltip" title="You have a client connected to this node ({{ array_get($model, 'location.nick') }})"><i class="fa fa-map-marker"></i></span>
            @endif
        </td>
        <td><span data-toggle="tooltip" title="{{ array_get($model, 'country.name') }}">{{ array_get($model, 'country.code') }}</span></td>
        <td>{{ array_get($model, 'users.current') }} / {{ array_get($model, 'users.peak') }}</td>
        <td>{{ date_difference(array_get($model, 'uptime')) }}</td>
    </tr>
@endforeach
</table>
