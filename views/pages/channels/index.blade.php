<div class="page-header">
    <h2>Network Channel Listings</h2>
</div>

<p>Below is a list of channels that are currently residing on the Darchoods Network. Channels highlighted with different colors indicate a special use. Green Channels denote Network Specific Channels, whereas Blue Channels are for community based channels.</p>

<table width="100%" border="0" class="table table-striped table-bordered">
<thead>
    <tr>
        <th width="5%">Channel Name</th>
        <th width="5%">Users</th>
        <th width="50%">Topic</th>
    </tr>
</thead>
<tbody>
@if (count($chans))
    @foreach($chans as $chan)
        <tr class="{{ $chan->extra or '' }}">
            <td>{{ HTML::link('http://widget.mibbit.com/?settings=3d76ae8aae223a0f553f71b8182f84bb&server=irc.darchoods.net&channel='.str_replace('#', '%23', $chan->channel), $chan->channel) }}</td>
            <td align="center">{{ $chan->currentusers }} / {{ $chan->maxusers }}</td>
            <td>{{ $chan->topic }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="danger">No channels found.</td>
    </tr>
@endif
</tbody>
</table>
