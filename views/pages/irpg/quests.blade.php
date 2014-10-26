@if (!count($quests))
    .
@else
    @foreach ($quests as $quest)
    <div class="well well-sm">
        <h4>Quest: <small>{{{ $quest['quest'] }}}</small></h4>
        <div class="row">
            <div class="col-md-3"><strong>Time to completion:</strong></div>
            <div class="col-md-9">{{ secs_to_h($quest['started']-time()) }}</div>
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
