<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style type="text/css" rel="stylesheet" media="all">

        </style>
    </head>
    <body>

        @if( $form->private )

        <p>
            A new encrypted form has been submitted. Please login and check.
        </p>

        <p>
            <a href="{{ env('APP_URL') }}admin/forms/{{ $form->id }}/submissions">View: {{ $form->title }} form submissions.</a>
        </p>

        @else
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    @foreach($formFields as $label => $value)
                    {{ $label }}: {{ is_array($value)? implode(', ',$value) : $value }}<br>
                    @endforeach
                </td>
            </tr>
        </table>
        @endif

    </body>
</html>
