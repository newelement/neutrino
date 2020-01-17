
        <draggable v-if="block.field_groups" v-model="block.field_groups" class="testimonials"  group="blockitems" handle=".block-item-drag-handle">
            <div v-for="(blockItem, itemIndex) in block.field_groups" class="testimonial" style="margin-bottom: 32px;">
                <block-item-actions :block-item="blockItem" :block-index="blockIndex"></block-item-actions>
                <div class="author-quote">
                    <div class="quote">
                        <tinymce-editor v-model="blockItem.fields[0].value" :init="$root.tinyInitInlineBlockquote" :tag-name="$root.tag.p" inline></tinymce-editor>
                    </div>
                    <div class="author">
                        <tinymce-editor v-model="blockItem.fields[1].value" :init="$root.tinyInitInlineParagraph" :tag-name="$root.tag.p" inline></tinymce-editor>
                    </div>
                </div>
                <div class="author-image" :style="'background-image: url(' +  blockItem.fields[2].value +')' ">
                    <child-block-chooser :field="blockItem.fields[2]" />
                    <child-blocks :field="blockItem.fields[2]" />
                    <button type="button" class="btn btn-dark btn-sm" @click.prevent="chooseBlockImage( blockIndex, itemIndex, 2 )">Choose Image</button>
                </div>
            </div>
        </draggable>


