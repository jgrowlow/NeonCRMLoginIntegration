<?php
namespace CustomApi\Controllers;

use CustomApi\Auth\UserAuthenticator;
use Flarum\User\User;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use CustomApi\Services\NeonCRMService;
use Flarum\Http\Middleware\CheckCsrfToken;
use Psr\Log\LoggerInterface;
use Illuminate\Validation\ValidationException;

class LoginController
{
    protected $neonService;
    protected $authenticator;
    protected $logger;

    public function __construct(NeonCRMService $neonService, UserAuthenticator $authenticator, LoggerInterface $logger)
    {
        $this->neonService = $neonService;
        $this->authenticator = $authenticator;
        $this->logger = $logger;
    }

    public function __invoke(Request $request)
    {
        // Validate CSRF token
        $csrfToken = $request->getHeader('X-CSRF-Token')[0] ?? '';
        $this->logger->info('CSRF Token: ' . $csrfToken);

        if (!CheckCsrfToken::isValid($csrfToken)) {
            $this->logger->error('CSRF token mismatch');
            return new JsonResponse(['error' => 'CSRF token mismatch'], 400);
        }

        try {
            $body = $request->getParsedBody();
            $email = Arr::get($body, 'email');
            $this->logger->info('Email: ' . $email);

            if (!$email) {
                throw new ValidationException(['error' => 'Email is required']);
            }

            // Use the UserAuthenticator to match the email
            $user = $this->authenticator->authenticate($email);

            if (!$user) {
                return new JsonResponse(['error' => 'User not found in forum'], 404);
            }

            RequestUtil::getActor($request)->login($user);

            $redirectUrl = getenv('FLARUM_REDIRECT_URL') ?: '/';
            $this->logger->info('Redirect URL: ' . $redirectUrl);

            return new RedirectResponse($redirectUrl);
        } catch (\Exception $e) {
            $this->logger->error('An unexpected error occurred: ' . $e->getMessage());
            return new JsonResponse(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}
