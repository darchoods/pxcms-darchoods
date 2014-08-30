<p>Below is a list of channels that are currently residing on the Darchoods Network. Channels highlighted in green denote Network Specific Channels.</p>

<table width="100%" border="0" class="table table-hover table-bordered">
<thead>
    <tr>
        <th width="5%">Channel Name</th>
        <th width="5%">Users</th>
        <th width="5%">Peak Users</th>
        <th width="50%">Topic</th>
    </tr>
</thead>
<tbody>
@if (count($chans))
    @foreach($chans as $chan)
        <tr class="{{ array_get($chan, 'extra') }}">
            <td>
                {{ array_get($chan, 'name') }}
            </td>
            <td align="center">{{{ array_get($chan, 'stats.current_users') }}}</td>
            <td align="center">
                <span data-toggle="tooltip" data-title="Peak hit on {{ date_carbon(array_get($chan, 'stats.peak_user_time'), 'd-m-Y') }}">
                    {{{ array_get($chan, 'stats.peak_users') }}}
                </span>
            </td>
            <td>{{ array_get($chan, 'topic.html') }} <span class="pull-right"><small> set by {{ profile(array_get($chan, 'topic.author')) }}</small></span></td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="danger">No channels found.</td>
    </tr>
@endif
</tbody>
</table>
