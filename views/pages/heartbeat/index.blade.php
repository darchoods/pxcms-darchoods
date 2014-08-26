<p>You can only connect to <strong>Darchoods IRC Network</strong> over SSL <small>(port 6697)</small>. Please make sure you enable the SSL Switch in your client when you try and connect. We also have a catch all address on <strong>irc.darchoods.net</strong> this will point you at any of our active network nodes. Alternatively you can use the server list below to choose the best location for you. Please note though, these servers may be taken offline or replaced completely as the need arises.</p>

@include('darchoods::pages.heartbeat._servers', compact('serverList'))
