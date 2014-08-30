<header class="row">
    <div class="col-md-12">
        <h4>Quick Network Stats</h4>
    </div>
</header>

<div class="row">
    <div class="col-md-6">
        <h5>Users on the network over the last 24 hours <small>(Peak Count: {{ array_get($userPeak, 'val') }} on {{ date_carbon(array_get($userPeak, 'time'), 'd-m-Y') }})</small></h5>
        <div id="users"></div>
    </div>
    <div class="col-md-6">
        <h5>Breakdown of User Clients</h5>
        <div id="clients"></div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h5>Breakdown of User Spread</h5>
        <div id="countries"></div>
        <div id="country"><i class="fa fa-info-circle"></i> Hover over a country</div>
    </div>
</div>
