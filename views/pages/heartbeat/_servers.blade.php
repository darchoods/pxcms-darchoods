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
            {{ ($model->online == 'Y' ? '<div class="label label-success">Online</div>' : '<div class="label label-danger">offline</div>') }} {{ $model->server }} :6697
            @if (isset($model->location))
            <span data-toggle="tooltip" title="You have a client connected to this node ({{ $model->location->nick }})"><i class="fa fa-map-marker"></i></span>
            @endif
        </td>
        <td><span data-toggle="tooltip" title="{{ $model->country }}">{{ $model->countrycode }}</span></td>
        <td>{{ $model->currentusers }} / {{ $model->maxusers }}</td>
        <td>{{ date_difference($model->uptime) }}</td>
    </tr>
@endforeach
</table>
