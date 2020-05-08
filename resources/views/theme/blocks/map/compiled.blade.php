<div class="map-embed-block">
    <iframe src="https://www.google.com/maps/embed/v1/place?key={{ env('GOOGLE_MAPS_API') }}&q={{ $data->field_groups[0]->fields[0]->value }}&zoom={{ $data->options_assoc['zoom']->value  }}" width="{{ $data->options_assoc['width']->value }}" height="{{ $data->options_assoc['height']->value }}" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
</div>
