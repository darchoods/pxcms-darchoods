<table class="table table-hover">
    <thead><tr>
        <th width="20%">Player</th>
        <th>Allignment</th>
        <th>Amulet <small>(level)</small></th>
        <th>Charm <small>(level)</small></th>
        <th>Helm <small>(level)</small></th>
        <th>Boots <small>(level)</small></th>
        <th>Gloves <small>(level)</small></th>
        <th>Ring <small>(level)</small></th>
        <th>Leggings <small>(level)</small></th>
        <th>Shield <small>(level)</small></th>
        <th>Tunic <small>(level)</small></th>
        <th>Weapon <small>(level)</small></th>
    </tr></thead>
    <tbody>
    @if(count($players))
        @foreach($players as $nick => $info)
    <tr>
        <td>
        @if(array_get($info, 'online') == '1')
            <span class="label label-success">&nbsp;</span>
        @else
            <span class="label label-danger">&nbsp;</span>
        @endif
        {{{ $nick }}} <small>({{{ array_get($info, 'level') }}})</small><br />{{{ array_get($info, '') }}}</td>
        <td>
        @if(array_get($info, 'alignment') == 'n')
            <div class="label label-info">Neutral</div>
        @else
            @if(array_get($info, 'alignment') == 'g')
                <div class="label label-success">Good</div>
            @else
                <div class="label label-danger">Evil</div>
            @endif
        @endif
        </td>
        <td>{{{ array_get($info, 'items.amulet') }}}</td>
        <td>{{{ array_get($info, 'items.charm') }}}</td>
        <td>{{{ array_get($info, 'items.helm') }}}</td>
        <td>{{{ array_get($info, 'items.boots') }}}</td>
        <td>{{{ array_get($info, 'items.gloves') }}}</td>
        <td>{{{ array_get($info, 'items.ring') }}}</td>
        <td>{{{ array_get($info, 'items.leggings') }}}</td>
        <td>{{{ array_get($info, 'items.shield') }}}</td>
        <td>{{{ array_get($info, 'items.tunic') }}}</td>
        <td>{{{ array_get($info, 'items.weapon') }}}</td>
    </tr>
        @endforeach
    @else
    <tr>
        <td colspan="10">No Players found in the IRPG Database.</td>
    </tr>
    @endif
    </tbody>
</table>
