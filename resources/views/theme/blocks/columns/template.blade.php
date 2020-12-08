<draggable v-if="block.field_groups"
v-model="block.field_groups"
ref="dragItem"
@end="dragEnd()"
@update="dragUpdate()"
@start="dragStart()"
class="columns-block"
:style="'justify-content: '+block.options[3].value+'; background-color: '+block.options[0].value+'; padding: '+ block.options[1].value + '; margin: '+ block.options[2].value"  group="blockitems" handle=".block-item-drag-handle">

<div
    v-for="(blockItem, itemIndex) in block.field_groups"
    class="block-column"
    :style="'flex-grow: '+blockItem.options[3].value+'; margin: '+ blockItem.options[2].value+'; padding: '+blockItem.options[1].value+'; background-color: '+blockItem.options[0].value+'; width: ' + blockItem.options[4].value">

    <block-item-actions :block="block" :block-item="blockItem" :block-index="blockIndex"></block-item-actions>

    <div class="block-column-inner">
        <tinymce-editor v-model="blockItem.fields[0].value" ref="editor" :init="$root.tinyInitInlineFreeText" style="margin-bottom: 24px"></tinymce-editor>
    </div>

</div>

</draggable>
