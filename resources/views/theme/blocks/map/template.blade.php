<div>
    <div class="form-block">
        <block-item-actions :block-item="block" :block-index="blockIndex"></block-item-actions>
        <div v-for="fields in block.field_groups">
            <input type="text" name="address" v-model="fields.fields[0].value" placeholder="Address" style="width: 100%">
        </div>
        <div class="map-block-placeholder">
            <i class="fal fa-map-marker-alt" style="margin-right: 12px"></i> Map
        </div>
    </div>
</div>
