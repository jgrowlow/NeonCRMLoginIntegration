<?php

namespace CustomApi\Auth;

use Flarum\User\Authenticator;
use Flarum\User\User;
use Illuminate\Contracts\Encryption\Encrypter;
use Psr\Log\LoggerInterface;

class UserAuthenticator implements Authenticator
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function authenticate($accountId)
    {
        // Attempt to find the user in Flarum by the NeonCRM accountId
        $user = User::where('neoncrm_account_id', $accountId)->first();

        if ($user) {
            // Successfully found the user, log them in
            return $user;
        } else {
            // No user found, log and return null
            $this->logger->warning("User not found for accountId: {$accountId}");
            return null;
        }
    }
}
