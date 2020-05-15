<?php
namespace Newelement\Neutrino\Events;
use Illuminate\Queue\SerializesModels;
use Newelement\Neutrino\Models\Form;

class FormSubmitted
{
    use SerializesModels;

	public $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }
}
