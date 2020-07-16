<div class="hero {{ $data->options_assoc['full_width']->value === '1'? 'alignfull' : ''  }} {{ $data->options_assoc['content_position']->value  }}"
    style="width: {{ $data->options_assoc['full_width']->value !== '1'? $data->options_assoc['width']->value : 'auto' }};
    height: {{ $data->options_assoc['height']->value }};
    background-image: url('{{ $data->field_groups[0]->fields[2]->value }}');
    background-size: {{ $data->options_assoc['background_size']->value }};
    background-position: {{ $data->options_assoc['background_position']->value }};
    ">
    @if( strlen($data->field_groups[0]->fields[1]->value) > 0 )
    <div class="hero-content">
        {!! $data->field_groups[0]->fields[1]->value !!}
    </div>
    @endif
    <div class="hero-overlay" style="background-color: {{ hex2rgba( $data->options_assoc['content_background_color']->value , $data->options_assoc['content_background_opacity']->value ) }}; "></div>
</div>
