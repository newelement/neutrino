<draggable v-if="block.field_groups" v-model="block.field_groups" ref="dragItem" @end="dragEnd()" @update="dragUpdate()" @start="dragStart()" class="carousel-block"  group="blockitems" handle=".block-item-drag-handle">
    <div v-for="(blockItem, itemIndex) in block.field_groups" class="carousel-block-slide" style="margin-bottom: 32px;">
        <block-item-actions :block="block" :block-item="blockItem" :block-index="blockIndex"></block-item-actions>
        <div class="carousel-image" :style="'background-image: url(' +  blockItem.fields[2].value +'); background-size: '+ blockItem.options[0].value + '; background-position: '+blockItem.options[1].value">
            <button type="button" class="btn btn-dark btn-sm" @click.prevent="chooseBlockImage( blockIndex, itemIndex, 2 )" style="margin-bottom: 24px">Choose Slide Image</button>
            <tinymce-editor v-model="blockItem.fields[1].value" ref="editor" :init="$root.tinyInitInlineFreeText" style="margin-bottom: 24px"></tinymce-editor>
            <input type="url" v-model="blockItem.fields[0].value" placeholder="Slide link (optional)">
        </div>
    </div>
</draggable>
