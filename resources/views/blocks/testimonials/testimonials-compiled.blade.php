
        <div class="testimonials">
            @foreach( $data->field_groups as $quote )
            <div class="testimonial">
                <div class="author-quote">
                    <div class="quote">
                        {{ getBlockField($quote, 'quote') }}
                    </div>
                    <div class="author">
                        {{ getBlockField($quote, 'author') }}
                    </div>
                </div>
                <div class="author-image" style="background-image: url('{{ getBlockField($quote, 'image') }}')">
                </div>
            </div>
            @endforeach
        </div>


