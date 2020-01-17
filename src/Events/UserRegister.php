<?php
namespace Newelement\Neutrino\Events;
use Illuminate\Queue\SerializesModels;
use Newelement\Neutrino\Models\User;

class UserRegister
{
    use SerializesModels;

	public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
