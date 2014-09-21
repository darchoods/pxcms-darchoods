<div class="row">
    <div class="col-md-3">@include('darchoods::settings.nav')</div>
    <div class="col-md-9">
        <h2>Your current API Keys</h2>

        <div class="alert alert-info">Information: These keys will give you access to the protected parts of the API. Either pass the key in through the '<strong>X-Auth-Token</strong>' HTTP Header, or a get/post parameter named '<strong>auth_token</strong>'. <br /><br /><strong>Also keep these keys safe</strong>.</div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <td width="44%">Key</td>
                    <td width="25%">Expiry</td>
                    <td width="31%">Actions</td>
                </tr>
            </thead>
            <tbody>
            @if (count($keys))
                @foreach($keys as $key)
                <tr>
                    <td>{{{ $key->description }}}<br /><span style="background: black; color: black; display: block">{{ $key->key }}</span></td>
                    <td>{{ array_get(date_array($key->expires_at), 'default') }}</td>
                    <td>
                        {{ Former::horizontal_open()->action(URL::route('darchoods.settings.apiremove'))->method('POST') }}
                            {{ Former::hidden('key_id', $key->id) }}
                            {{ Former::framework('Nude') }}
                            {{ Former::checkbox('confirm') }}
                            {{ Former::framework('TwitterBootstrap3') }}

                            <button class="btn-labeled btn btn-xs btn-danger" type="submit">
                                <span class="btn-label"><i class="fa fa-times"></i></span> Delete Key
                            </button>
                        {{ Former::close() }}
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="warning">You currently don't have any API Keys registered.</td>
                </tr>
            @endif
            </tbody>
        </table>

        <hr />
        <h2>Add a new key</h2>

        {{ Former::horizontal_open() }}
            {{ Former::text('description') }}
            {{ Former::select('expires_at')->options([
                '6'  => '6 Months',
                '12' => '12 Months',
                '18' => '18 Months',
            ]) }}

            <button class="btn-labeled btn btn-success pull-right" type="submit">
                <span class="btn-label"><i class="fa fa-plus"></i></span> Generate API Key
            </button>

        {{ Former::close() }}
    </div>
</div>
