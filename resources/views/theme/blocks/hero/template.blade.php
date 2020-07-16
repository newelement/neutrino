<div>
    <div class="hero-block" :style="'background-image: url(' + block.field_groups[0].fields[2].value +'); background-size: '+ block.options[3].value + '; background-position: '+block.options[4].value">
        <block-item-actions  :block-item="block" :block-index="blockIndex"></block-item-actions>
        <button type="button" class="btn btn-dark btn-sm" @click.prevent="chooseBlockImage( blockIndex, 0, 2 )" style="margin-bottom: 24px">Choose Hero Image</button>
        <tinymce-editor v-model="block.field_groups[0].fields[1].value" ref="editor" :init="$root.tinyInitInlineFreeText" style="margin-bottom: 24px"></tinymce-editor>
    </div>
</div>
