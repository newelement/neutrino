<div class="form-container">
    @php
    $args = [ 'show_title' => (int) $data->options_assoc['show_title']->value ];
    @endphp
    {{ getFormHTML($data->field_groups[0]->fields[0]->value, $args) }}
</div>
