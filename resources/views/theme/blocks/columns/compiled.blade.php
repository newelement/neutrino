<div class="container">
<div class="block-columns-row" style="@if($data->options_assoc['background_color']->value)background-color: {{$data->options_assoc['background_color']->value}};@endif  @if($data->options_assoc['alignment']->value)justify-content: {{ $data->options_assoc['alignment']->value }};@endif @if($data->options_assoc['padding']->value)padding: {{ $data->options_assoc['padding']->value }};@endif @if($data->options_assoc['margin']->value)margin: {{ $data->options_assoc['margin']->value }};@endif">
@foreach( $data->field_groups as $col )
    <div class="flex-col" style="@if($col->options[0]->value)background-color: {{ $col->options[0]->value }};@endif @if($col->options[1]->value)padding: {{ $col->options[1]->value }};@endif @if($col->options[2]->value)margin: {{ $col->options[2]->value }};@endif @if($col->options[3]->value)flex-grow: {{ $col->options[3]->value }};@endif @if($col->options[4]->value)width: {{ $col->options[4]->value }} @endif">
        {!! $col->fields[0]->value !!}
    </div>
@endforeach
</div>
</div>
