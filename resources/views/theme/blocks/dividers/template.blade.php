<div>
    <div class="divider-block" :style="'background-image: url(' + block.field_groups[0].fields[2].value +'); background-size: '+ block.options[3].value + '; background-position: '+block.options[4].value + '; background-color: '+block.options[5].value">
        <block-item-actions  :block-item="block" :block-index="blockIndex"></block-item-actions>
        <button type="button" class="btn btn-dark btn-sm" @click.prevent="chooseBlockImage( blockIndex, 0, 2 )">Choose Divider Image (optional)</button>
    </div>
</div>
