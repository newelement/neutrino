<?php

namespace Newelement\Neutrino\Http\Controllers;

use App\Http\Controllers\Controller;
use Newelement\Neutrino\Models\Form;
use Newelement\Neutrino\Models\FormField;
use Newelement\Neutrino\Mail\FormSubmitted;
use Illuminate\Support\Facades\Mail;

class NeutrinoEmailController extends Controller
{

    public function processForm($form, $data, $files)
    {

        //dd($form);
        if( isset($data['_token']) ){
            unset($data['_token']);
        }
        if( isset($data['q']) ){
            unset($data['q']);
        }
        if( isset($data['valid_from']) ){
            unset($data['valid_from']);
        }
        if( isset($data['id']) ){
            unset($data['id']);
        }

        if( isset($data['gr_score']) ){
            //unset($data['gr_score']);
        }

        //if( isset( $data['recaptcha_challenge_field'] ) )

        $formFields = [];
        $fields = $form->fields()->get();
        foreach( $fields as $field ){
            $formFields[$field->field_label] = isset($data[$field->field_name])? $data[$field->field_name] : '';
        }

        // $form->email_to
        $tos = explode( ',', $form->email_to);
        foreach( $tos as $to ){
            Mail::to(trim($to))->send(new FormSubmitted($form, $data, $files, $formFields));
        }

    }

    public function processPrivateForm($form)
    {
        $data = [];
        $files = [];
        $formFields = [];
        $tos = explode( ',', $form->email_to);
        foreach( $tos as $to ){
            Mail::to(trim($to))->send(new FormSubmitted($form,  $data, $files, $formFields));
        }
    }
}
