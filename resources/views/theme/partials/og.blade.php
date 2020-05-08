<meta property="og:title" content="{{ $data->title }}" />
<meta property="og:description" content="{{ $data->meta_description }}" />
@if( isset($data->social_image) && strlen($data->social_image) )
@php
$socialImages = getImageSizes($data->social_image)
@endphp
<meta property="og:image" content="{{ env('APP_URL') }}{{ $socialImages['large'] }}">
@endif
