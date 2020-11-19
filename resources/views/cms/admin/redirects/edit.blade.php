@extends('neutrino::admin.template.header-footer')
@section('title', 'Edit Redirect | ')
@section('content')
    <div class="container">
        <div class="content full">

            <h2>Edit Redirect <a class="headline-btn" href="/admin/redirects" role="button">Back to Redirects</a></h2>

            <form action="/admin/redirects/{{ $redirect->id }}" method="post" style="margin-bottom: 48px;">
                @csrf
                @method('put')
                <div class="form-row">
                    <label class="label-col" for="old-url">Old URL</label>
                    <div class="input-col">
                        <input id="old-url" type="text" name="old_url" value="{{ $redirect->old_url }}">
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-col" for="new-url">New URL</label>
                    <div class="input-col">
                        <input id="new-url" type="text" name="new_url" value="{{ $redirect->new_url }}">
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-col" for="status">Status Code</label>
                    <div class="input-col">
                        <div class="select-wrapper">
                            <select id="status" name="status" required>
                                <option value="301" {{ $redirect->status === 301? 'selected="selected"' : '' }}>Permanent (301)</option>
                                <option value="302" {{ $redirect->status === 302? 'selected="selected"' : '' }}>Normal (302)</option>
                                <option value="307" {{ $redirect->status === 307? 'selected="selected"' : '' }}>Temporary (307)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn">Update Redirect</button>

            </form>

        </div>
    </div>
@endsection
