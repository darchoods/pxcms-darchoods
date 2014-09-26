<?php
    if (empty($db[$key])) {
        return '';
    }
?>
<table class="table table-hover">
    <thead>
        <tr>
            @foreach($db[$key]['output'] as $label => $column)
            <th>{{ $label }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach($db[$key]['data'] as $row)
        <tr>
            @foreach($db[$key]['output'] as $label => $column)
            <td>{{ $row[$column] }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
