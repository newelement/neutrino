<?php
namespace Newelement\Neutrino\Events;
use Illuminate\Queue\SerializesModels;
use Newelement\Neutrino\Models\Form;

class FormSubmitted
{
    use SerializesModels;

	public $form;
    public $data;

    public function __construct(Form $form, $data)
    {
        $this->form = $form;
        $this->data = $data;
    }
}
