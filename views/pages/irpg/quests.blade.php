@if (!count($quests))
    <p>There are no quests currently being undertaken.</p>
@else
    @foreach ($quests as $quest)
    <div class="well well-sm">
        <h4>Quest: <small>{{{ $quest['quest'] }}}</small></h4>
        <div class="row">
        @if ($quest['type'] == 1)
            <div class="col-md-3"><strong>Time to completion:</strong></div>
            <div class="col-md-9">{{ secs_to_h($quest['started']-time()) }}</div>
        @else
            <?php $position = ($quest['stage'] == 1 ? 'p1' : 'p2'); ?>
            <div class="col-md-3"><strong>Current Goal:</strong></div>
            <div class="col-md-9">{{ '[',array_get($quest, 'goals.'.$position.'.0'),',',array_get($quest, 'goals.'.$position.'.1'),']' }}</div>
        @endif
        </div>
        <div class="row">
            <div class="col-md-3"><strong>Players Involved:</strong></div>
            <div class="col-md-9">
                <ul>
                    @foreach ($quest['players'] as $player)
                    <li>

                        {{{ $player['name'] }}}
                        @if ($quest['type'] == 2)

                            {{ '[',$player['x'],',',$player['y'],']' }}

                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
@endif
