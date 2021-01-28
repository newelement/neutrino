@extends('neutrino::admin.template.header-footer')
@section('title', 'Shortcodes | ')
@section('content')
    <div class="container">
        <div class="content terms-content">
            <h2>Shortcodes</h2>

            <table cellpadding="0" cellspacing="0" class="table">
                <thead>
                    <tr>
                        <th class="text-left">Title</th>
                        <th>Shortcode</th>
                        <th width="100">Edit</th>
                        <th width="80"></th>
                    </tr>
                </thead>
                <tbody class="tax-type-table">
                @foreach( $shortcodes as $shortcode )
                    <tr>
                        <td class="tax-item" data-id="{{ $shortcode->id }}" data-label="Title">
                            <a href="/admin/shortcodes/{{ $shortcode->id }}">{{ $shortcode->title }}</a>
                        </td>
                        <td data-label="Slug" class="text-center">
                            [{{ $shortcode->slug }}]
                        </td>
                        <td data-label="Edit Terms" class="text-center">
                            <a href="/admin/shortcodes/{{$shortcode->id}}">Edit</a>
                        </td>
                        <td data-label="Delete" class="text-center">
                            <form action="/admin/shortcodes/{{$shortcode->id}}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="delete-btn-taxonomy">&times;</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="pagination">{{ $shortcodes->links() }}</div>

        </div>

        <aside class="sidebar terms-sidebar">

            <form action="/admin/shortcodes" method="post">
            @csrf

                <div class="side-fields">

                    <div class="form-row">
                        <label class="label-col" for="title">Shortcode Title</label>
                        <div class="input-col">
                            <input id="title" type="text" name="title" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="label-col" for="title">Code</label>
                        <div class="input-col">
                            <textarea id="embed" name="embed"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn full">Create Shortcode</button>

                </div>

            </form>

        </aside>
    </div>
@endsection
