<p>Using the API you can access various statistics about the IRC Network. This api will return JSON regardless of headers set, this may change in the future if there is enough request for it.</p>

@if (count($api))
    @foreach ($api as $api)
    <h4>{{ array_get($api, 'description') }}</h4>
    <pre>{{ array_get($api, 'method') }} <a href="{{ Url::to(array_get($api, 'url')) }}">{{ array_get($api, 'url') }}</a></pre>
    @if (count($api['vars']))
    <table class="table table-hover">
        <tr>
            <th>Variable</th>
            <th>Usable Value(s)</th>
            <th>Use</th>
        </tr>
        @foreach ($api['vars'] as $var)
        <tr>
            <td>{{ array_get($var, 'var') }}</td>
            <td>{{ array_get($var, 'value') }}</td>
            <td>{{ array_get($var, 'use') }}</td>
        </tr>
        @endforeach
    </table>
    @endif
    @endforeach
@endif
