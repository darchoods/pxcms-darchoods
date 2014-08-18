<div class="page-header">
    <h2>Network Channel Listings</h2>
</div>

<p>Below is a list of channels that are currently residing on the Darchoods Network. Channels highlighted with different colors indicate a special use. Green Channels denote Network Specific Channels, whereas Blue Channels are for community based channels.</p>

<table width="100%" border="0" class="table table-bordered table-hover">
<thead>
    <tr>
        <th>Channel Name</th>
        <th>Stats</th>
        <th>Users</th>
        <th>Topic</th>
    </tr>
</thead>
<tbody>
@if (count($chans))
    @foreach($chans as $chan)
        <tr class="{{ $chan['EXTRA'] }}">
            <td>{{ $chan['NAME'] }}</td>
            <td>

            @if($chan['STATS'] !== null)
                <a href="{{ $chan['STATS'] }}" data-toggle="modal" data-title="{{ $chan['NAME'] }} Stats">Stats</a>
            @else
                <font class="muted">Stats</font>
            @endif

            </td>
            <td align="center">{{ $chan['COUNT'] }}</td>
            <td>{{ $chan['TOPIC'] }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="danger">No channels found.</td>
    </tr>
@endif
</tbody>
</table>
