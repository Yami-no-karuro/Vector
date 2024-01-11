<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Vector\Module\ApplicationLogger\SqlLogger;
use Vector\Module\Security\WebToken;
use Vector\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class LoginController extends FrontendController
{
    protected function register(): void
    {
        Router::route(['GET'], '^/login/?$', [$this, 'loginViewAction']);
        Router::route(['POST'], '^/login/submit/?$', [$this, 'loginSubmitAction']);
        Router::route(['GET'], '^/logout/?$', [$this, 'logoutAction']);
    }

    /**
     * Route: '/login'
     * Methods: GET
     * @return Response
     */
    public function loginViewAction(): Response
    {

        /**
         * @var string $html
         * Retrive raw view html from the twig template engine.
         */
        $html = $this->template->render('admin/login.html.twig', [
            'title' => 'Vector',
            'description' => 'A simple HttpFoundation framework for PHP.',
            'formMethod' => 'POST',
            'formAction' => '/login/submit'
        ]);

        return new Response($html, Response::HTTP_OK);
    }

    /**
     * Route '/login/submit'
     * Methods: POST
     * @param Request $request
     * @return RedirectResponse
     */
    public function loginSubmitAction(Request $request): RedirectResponse
    {

        /**
         * @var UserRepository $repository
         * @var string $email
         * If the provided email is valid search for an existing user.
         */
        $repository = UserRepository::getInstance();
        if (null !== ($email = $request->get('email')) && false !== filter_var($email, FILTER_VALIDATE_EMAIL)) {

            /**
             * @var array $user
             * Looks for valid users by email.
             */
            if (null !== ($user = $repository->getByEmail($email))) {

                /**
                 * @var string $password
                 * On password match set the autentication cookie and redirect to /admin.
                 * Redirect back with "?success=false" on failure.
                 */
                $password = $user['password'];
                if (hash('sha256', $request->get('password', '')) === $password) {

                    /**
                     * @var Cookie $cookie
                     * Authentication cookie is set.
                     */
                    $cookie = new Cookie('Auth-Token', WebToken::generate([
                        'rsid' => $user['ID'],
                        'time' => microtime()
                    ], $request));

                    $cookie->withHttpOnly(true);
                    $cookie->withSecure(true);

                    $response = new RedirectResponse('/admin', Response::HTTP_FOUND);
                    $response->headers->setCookie($cookie);
                    return $response;
                }
            }
        }

        /**
         * @var SqlLogger $logger
         * Logging login attempt to keep track of user and bot activities.
         */
        $logger = new SqlLogger('auth');
        $logger->write('Client: "' . $request->getClientIp() . '" attempted to login with incorrect credentials.');

        return new RedirectResponse(
            '/login?success=false',
            Response::HTTP_FOUND
        );
    }

    /**
     * Route '/logout'
     * Methods: GET
     * @return RedirectResponse
     */
    public function logoutAction(): RedirectResponse
    {
        $response = new RedirectResponse('/login', Response::HTTP_FOUND);
        $response->headers->clearCookie('Auth-Token');
        return $response;
    }

}