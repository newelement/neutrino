<div>
    <div class="gallery-block">
        <block-item-actions :block-item="block" :block-index="blockIndex"></block-item-actions>
        <div class="select-wrapper">
            <div v-for="fields in block.field_groups">
                <select name="gallery" v-model="fields.fields[0].value">
                    <option value="">Choose Gallery</option>
                    @foreach($galleries as $gallery)
                    <option value="{{ $gallery->id }}">{{ $gallery->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="gallery-block-placeholder">
            <i class="fal fa-images" style="margin-right: 12px"></i> Gallery
        </div>
    </div>
</div>
