<?php


namespace Filesharing\Middleware;


class AuthMiddleware
{
    private $auth;

    /**
     * AuthMiddleware constructor.
     * @param $auth
     */
    public function __construct($auth)
    {
        $this->auth = $auth;
    }

    public function __invoke($request, $response, $next)
    {

        if ($this->auth->isLoggedIn($request->getCookieParam('hash'), $response)) {
            $response = $next($request, $response);

            return $response;
        } else {
            return $response->withRedirect('/', 303);
        }

    }

}