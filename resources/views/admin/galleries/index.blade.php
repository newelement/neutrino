@extends('neutrino::admin.template.header-footer')
@section('title', 'Galleries | ')
@section('content')
    <div class="container">
        <div class="content full">
            <h2>Galleries <a class="headline-btn" href="/admin/gallery" role="button">Create New Gallery</a></h2>

            <table cellpadding="0" cellspacing="0" class="table setting-table">
                <thead>
                    <tr>
                        <th class="text-left">Gallery</th>
                        <th class="text-center">Shortcode</th>
                        <th width="80">Edit</th>
                        <th width="60"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach( $galleries as $gallery )
                    <tr>
                        <td data-label="Title" class="text-left">
                            <a href="/admin/galleries/{{$gallery->id}}">{{ $gallery->title }}</a>
                        </td>
                        <td data-label="Shortcode" class="text-center">
                        [gallery id='{{ $gallery->id }}' show_title='true' show_description='true']
                        </td>
                        <td data-label="Edit" class="text-center">
                            <a href="/admin/galleries/{{$gallery->id}}">Edit</a>
                        </td>
                        <td data-label="Delete">
                            <form action="/admin/galleries/{{$gallery->id}}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="delete-btn">&times;</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection
