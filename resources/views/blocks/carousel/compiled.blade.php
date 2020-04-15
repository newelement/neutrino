<div class="carousel" style="width: {{ $data->options_assoc['width']->value }}">
    <div class="carousel-slides" style="width: {{ $data->options_assoc['width']->value }}" >
        @foreach( $data->field_groups as $slide )
        @php
        $openTag = strlen($slide->fields[0]->value)? '<a href="'.$slide->fields[0]->value.'" ' : '<div ';
        $closeTag = strlen($slide->fields[0]->value)? '</a>' : '</div>';
        @endphp
        {{ $openTag }} class="slide"
            style="background-image: url('{{ $slide->fields[2]->value }}'); background-position: {{ $slide->options[1]->value }}; background-size: {{ $slide->options[0]->value }}; background-repeat: no-repeat; height: {{ $data->options_assoc['height']->value }} ">
            @if( strlen($slide->fields[1]->value) )
            <div class="slide-content" style="background-color: {{ hex2rgba( $slide->options[2]->value, $slide->options[3]->value) }}">
                {!! $slide->fields[1]->value !!}
            </div>
            @endif
        {{ $closeTag }}
        @endforeach
    </div>
</div>
