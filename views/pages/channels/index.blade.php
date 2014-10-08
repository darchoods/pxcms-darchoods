<p>Below is a list of channels that are currently residing on the Darchoods Network.</p>
<div class="channels">
    @if (count($chans))
        @foreach($chans as $chan)
        <div class="channel-container" data-channel="{{{ Str::lower(array_get($chan, 'name')) }}}">
            <span class="channel-title">{{{ array_get($chan, 'name') }}}</span>
            <span class="current-users">Current Users: {{{ array_get($chan, 'stats.current_users') }}}</span>
            <span class="user-peak" data-toggle="tooltip" data-title="Peak hit on {{ date_carbon(array_get($chan, 'stats.peak_user_time'), 'd-m-Y') }}">User Peak: {{{ array_get($chan, 'stats.peak_users') }}}</span>
        </div>
        @endforeach
    @else
        <p class="alert alert-danger">No channels found.</p>
    @endif
</div>
