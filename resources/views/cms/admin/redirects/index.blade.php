@extends('neutrino::admin.template.header-footer')
@section('title', 'Redirects | ')
@section('content')
    <div class="container">
        <div class="content full">
            <h2>Redirects</h2>

            <h4>Create New Redirect</h4>

            <form action="/admin/redirects" method="post" style="margin-bottom: 48px;">
                @csrf
                <div class="form-row">
                    <label class="label-col" for="old-url">Old URL</label>
                    <div class="input-col">
                        <input id="old-url" type="text" name="old_url" value="{{ old('old_url') }}">
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-col" for="new-url">New URL</label>
                    <div class="input-col">
                        <input id="new-url" type="text" name="new_url" value="{{ old('new_url') }}">
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-col" for="status">Status Code</label>
                    <div class="input-col">
                        <div class="select-wrapper">
                            <select id="status" name="status" required>
                                <option value="301">Permanent (301)</option>
                                <option value="302">Normal (302)</option>
                                <option value="307">Temporary (307)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn">Create Redirect</button>

            </form>

            <h4>Current Redirects</h4>

            <table cellpadding="0" cellspacing="0" class="table">
            <thead>
                <tr>
                    <th class="text-left">Old URL</th>
                    <th class="text-left">New URL</th>
                    <th class="text-center" width="100">Status</th>
                    <th class="text-center" width="100">Edit</th>
                    <th width="100"></th>
                </tr>
            </thead>
            <tbody>
            @foreach( $redirects as $redirect )
                <tr>
                    <td data-label="Old URL">{{ $redirect->old_url }}</td>
                    <td data-label="New URL">{{ $redirect->new_url }}</td>
                    <td data-label="Status" class="text-center">{{ $redirect->status }}</td>
                    <td data-label="Edit" class="text-center">
                        <a href="/admin/redirects/{{ $redirect->id }}">Edit</a>
                    </td>
                    <td data-label="Delete" class="text-center">
                        <form action="/admin/redirects/{{$redirect->id}}" method="post">
                            @csrf
                            @method('delete')
                            <button type="submit" class="delete-btn">&times;</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>


            <div class="pagination-links">
                {{ $redirects->links() }}
            </div>

        </div>
    </div>
@endsection
