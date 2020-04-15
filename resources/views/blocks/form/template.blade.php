<div>
    <div class="form-block">
        <block-item-actions :block-item="block" :block-index="blockIndex"></block-item-actions>
        <div class="select-wrapper">
            <div v-for="fields in block.field_groups">
                <select name="form_id" v-model="fields.fields[0].value">
                    <option value="">Choose Form</option>
                    @foreach($forms as $form)
                    <option value="{{ $form->id }}">{{ $form->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-block-placeholder">
            <i class="fal fa-clipboard-list-check" style="margin-right: 12px"></i> Form
        </div>
    </div>
</div>
