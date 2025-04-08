<?php
namespace CustomApi\Auth;

use Flarum\User\Authenticator;
use Flarum\User\User;
use Illuminate\Contracts\Encryption\Encrypter;
use Psr\Log\LoggerInterface;
use Flarum\Http\Redirector;

class UserAuthenticator implements Authenticator
{
    protected $logger;
    protected $redirector;

    public function __construct(LoggerInterface $logger, Redirector $redirector)
    {
        $this->logger = $logger;
        $this->redirector = $redirector;
    }

    /**
     * Authenticate the user by their NeonCRM accountId.
     *
     * @param string $email
     * @return User|null
     */
    public function authenticate($email)
    {
        // Attempt to find the user by email in Flarum
        $user = User::where('email', $email)->first();
        
        if ($user) {
            // Successfully found the user, log them in
            return $user;
        }

        // Log if no user is found
        $this->logger->warning("No user found for email: {$email}");
        
        // Redirect to the specified URL if no user is found
        return $this->redirector->to('https://www.hopecommunitycenter.org/join');
    }
}
