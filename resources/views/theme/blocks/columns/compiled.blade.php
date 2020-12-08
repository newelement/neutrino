<div class="container">
    <div class="block-columns-row" style="background-color: {{$data->options_assoc['background_color']->value}}; justify-content: {{ $data->options_assoc['alignment']->value }}; padding: {{ $data->options_assoc['padding']->value }}; margin: {{ $data->options_assoc['margin']->value }};">
    @foreach( $data->field_groups as $col )
        <div class="flex-col" style="background-color: {{ $col->options[0]->value }}; padding: {{ $col->options[1]->value }}; margin: {{ $col->options[2]->value }}; flex-grow: {{ $col->options[3]->value }}; width: {{ $col->options[4]->value }}">
            {!! $col->fields[0]->value !!}
        </div>
    @endforeach
    </div>
</div>
