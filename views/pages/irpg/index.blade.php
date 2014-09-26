<div class="row">
    <div class="col-md-4">
        <div class="page-header">
            <h2>Top {{ $data['actualTotal']['keep'] }} in battle level</h2>
        </div>

        @include('darchoods::pages.irpg._table', ['key' => 'actualTotal', 'db' => $data])
    </div>
    <div class="col-md-4">
        <div class="page-header">
            <h2>Next {{ $data['secs']['keep'] }} to level up</h2>
        </div>

        @include('darchoods::pages.irpg._table', ['key' => 'secs', 'db' => $data])
    </div>
    <div class="col-md-4">
        <div class="page-header">
            <h2>Game Stats</h2>
        </div>

        @include('darchoods::pages.irpg._table', ['key' => 'stats', 'db' => $data])
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="page-header">
            <h2>Online vs Offline</h2>
        </div>

        <div id="onffline"></div>
    </div>
    <div class="col-md-4">
        <div class="page-header">
            <h2>Good vs Evil</h2>
        </div>

        <div id="alignment"></div>
    </div>
    <div class="col-md-4">
        <div class="page-header">
            <h2>Level Spread</h2>
        </div>

        <div id="spread"></div>
    </div>
</div>
