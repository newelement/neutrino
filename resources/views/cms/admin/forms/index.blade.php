@extends('neutrino::admin.template.header-footer')
@section('title', 'Forms | ')
@section('content')
	<div class="container">
		<div class="content full">
			<h2>Forms <a class="headline-btn" href="/admin/form" role="button">Create New Form</a></h2>

			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<th class="text-left">Title</th>
						<th class="text-center">Form ID</th>
						<th class="text-center">Shortcode
                            <span class="help-info">
                                <i class="fal fa-question-circle"></i>
                                <div class="help-info-content">
                                    <p>
                                    Copy and paste this optional shortcode into an editor to display a form.
                                    </p>
                                    <p>
                                    Options:<br>
                                    <strong>id:</strong> [The Form ID]<br>
                                    <strong>show_title:</strong> true/false
                                    </p>
                                </div>
                            </span>
                        </th>
                        <th class="text-center">Submissions</th>
						<th width="120">Edit Fields</th>
						<th width="100"></th>
					</tr>
				</thead>
				<tbody>
				@foreach( $forms as $form )
					<tr>
						<td data-label="Title">
							<a href="/admin/forms/{{ $form->id }}">{{ $form->title }}</a>
						</td>
						<td data-label="Form ID" class="text-center">{{ $form->id }}</td>
						<td data-label="Shortcode" class="text-center">[form id='{{ $form->id }}' show_title='true']</td>
                        <td class="text-center" data-label="Submissions"><a href="/admin/forms/{{$form->id}}/submissions">Submissions</a></td>
						<td data-label="Edit" class="text-center">
							<a href="/admin/forms/{{ $form->id }}/fields">Edit Fields</a>
						</td>
						<td data-label="Delete">
							<form action="/admin/forms/{{$form->id}}" method="post">
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
				{{ $forms->links() }}
			</div>
		</div>
	</div>
@endsection
