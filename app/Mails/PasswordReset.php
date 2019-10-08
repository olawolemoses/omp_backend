<?php


namespace App\Mails;


use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Builds the message
     * @return PasswordReset
     */
    public function build()
    {
        return $this->subject('OMP: Password Reset')
                ->markdown('mails.passwords.reset', ['user' => $this->user]);
    }
}
