<div class="divider {{ $data->options_assoc['full_width']->value === '1'? 'alignfull' : ''  }}"
    style="width: {{ $data->options_assoc['full_width']->value !== '1'? $data->options_assoc['width']->value : 'auto' }};
    height: {{ $data->options_assoc['height']->value }};
    @if( strlen($data->field_groups[0]->fields[2]->value) )
    background-image: url('{{ $data->field_groups[0]->fields[2]->value }}');
    @endif
    @if( strlen($data->options_assoc['background_color']->value ) )
    background-color: {{ $data->options_assoc['background_color']->value }};
    @endif
    background-size: {{ $data->options_assoc['background_size']->value }};
    background-position: {{ $data->options_assoc['background_position']->value }};
    ">
</div>
