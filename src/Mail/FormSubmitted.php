<?php

namespace Newelement\Neutrino\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $form;
    public $data;
    public $files;
    public $formFields;

    /**
    * Create a new message instance.
    *
    * @return void
    */
    public function __construct($form, $data, $files, $formFields)
    {
        $this->form = $form;
        $this->data = $data;
        $this->files = $files;
        $this->formFields = $formFields;
    }

    /**
    * Build the message.
    *
    * @return $this
    */
    public function build()
    {

        $email = $this->subject($this->form->subject)->view('neutrino::emails.forms');
        foreach( $this->files as $file ){

            $email->attach($file['path'],
                [
                    'as' => $file['as'],
                    'mime' => $file['mime'],
            ]);
        }

        return $email;
    }
}
