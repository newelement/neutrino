<?php

namespace Newelement\Neutrino\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

	public $email;
	public $token;

    public function __construct($email, $token)
    {
        $this->email = $email;
		$this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
				->subject('Your password reset link')
				->view('neutrino::emails.password-reset');
    }
}
