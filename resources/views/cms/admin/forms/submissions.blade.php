@extends('neutrino::admin.template.header-footer')
@section('title', 'Form Submissions | ')
@section('content')
<div class="container">
    <div class="content full">
        <h2>Form: {{ $data->title }} Submissions</h2>

        <table cellpadding="0" cellspacing="0" class="table">
            <thead>
                <tr>
                    <th class="text-left" width="300">Title</th>
                    <th class="text-left">Data</th>
                    <th width="180">Submitted On</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $data->submissions as $submission )
                <tr>
                    <td data-label="Title">
                        <a href="/admin/forms/{{ $data->id }}">{{ $data->title }}</a>
                    </td>
                    <td data-label="Data" class="text-left">
                        @php
                        $fields = json_decode( $submission->fields );
                        $files = json_decode( $submission->files );
                        @endphp
                        <p>
                            <small>
                                @foreach( $fields as $key => $value )
                                {{ $key }}: {{ $value }} <br>
                                @endforeach
                            </small>
                        </p>
                        <small>
                            @foreach( (array) $files as $file )
                            <a href="{{ $file->url }}">{{ $file->as }}</a><br>
                            @endforeach
                        </small>
                    </td>
                    <td data-label="Submitted On" class="text-center">
                        {{ $submission->created_at->timezone( config('neutrino.timezone') )->format('m-j-y g:i a') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-links">
            {{ $data->submissions->links() }}
        </div>
    </div>
</div>
@endsection
