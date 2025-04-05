<?php
use Flarum\Extend;
use Flarum\Http\Middleware\CheckCsrfToken;
use CustomApi\Controllers\LoginController;
use CustomApi\Providers\CustomApiServiceProvider;
use CustomApi\Auth\UserAuthenticator;

return [
    (new Extend\Routes('api'))
        ->post('/neoncrm/login', 'neoncrm.login', LoginController::class),

    (new Extend\ServiceProvider())
        ->register(CustomApiServiceProvider::class),

    // Register the custom UserAuthenticator as a service
    (new Extend\ServiceProvider())
        ->register(function () {
            return new UserAuthenticator();
        }),

    // Add CheckCsrfToken middleware to the API pipeline
    (new Extend\Middleware('api'))
        ->add(CheckCsrfToken::class),
];
