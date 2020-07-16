<section class="carousel {{ $data->options_assoc['full_width']->value === '1'? 'alignfull' : ''  }}" style="width: {{ $data->options_assoc['full_width']->value !== '1'? $data->options_assoc['width']->value : 'auto' }}">
    <div class="carousel-slides" style="width: {{ $data->options_assoc['width']->value }}">
        @foreach( $data->field_groups as $slide )
        @php
        $openTag = strlen($slide->fields[0]->value)? '<a href="'.$slide->fields[0]->value.'" ' : '<div ';
        $closeTag = strlen($slide->fields[0]->value)? '</a>' : '</div>';
        @endphp
        {{ $openTag }} class="slide"
        style="background-image: url('{{ $slide->fields[2]->value }}'); background-position: {{ $slide->options[1]->value }}; background-size: {{ $slide->options[0]->value }}; background-repeat: no-repeat; height: {{ $data->options_assoc['height']->value }} ">
        @if( strlen($slide->fields[1]->value) )
            <div class="slide-content" style="background-color: {{ hex2rgba( $slide->options[2]->value, $slide->options[3]->value) }}">
                <div class="inner">
                    {!! $slide->fields[1]->value !!}
                </div>
            </div>
        @endif
        {{ $closeTag }}
        @endforeach
    </div>
    <div class="hero-dots"></div>
</section>

@prepend('footerscripts')
<script>
window.slickSettings = { dots: true, autoplay: true, autplaySpeed: 3000 };
</script>
@endprepend
