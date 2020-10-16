@extends('neutrino::admin.template.header-footer')
@section('title', 'Form Submissions | ')
@section('content')
<div class="container">
    <div class="content full">
        <h2>Form: {{ $data->title }} Submissions</h2>

        <table cellpadding="0" cellspacing="0" class="table">
            <thead>
                <tr>
                    <th class="text-left">Data</th>
                    <th width="180">Submitted On</th>
                </tr>
            </thead>
            <tbody>
                @foreach( $data->submissions as $submission )
                <tr>
                    <td data-label="Data" class="text-left">
                        @php
                        $fields = json_decode( $submission->fields, true );
                        $files = json_decode( $submission->files, true );
                        unset($fields['_token']);
                        unset($fields['form_id']);
                        unset($fields['valid_from']);
                        foreach($fields as $key => $value){
                            if (strpos($key, 'my_name_') === 0){
                              unset($fields[$key]);
                            }
                        }

                        @endphp
                        <p style="margin-bottom: 0;">
                            <small>
                                @foreach( $fields as $key => $value )
                                {{ $key }}: @if( !is_array( $value) ) {{ $value }} @endif<br>
                                @endforeach
                            </small>
                        </p>
                        <small>
                            @foreach( (array) $files as $file )
                                @if( $data->private )
                                <a href="/admin/private-file?file={{ $file['url'] }}">{{ $file['as'] }}</a><br>
                                @else
                                <a href="{{ $file['url'] }}">{{ $file['as'] }}</a><br>
                                @endif

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
