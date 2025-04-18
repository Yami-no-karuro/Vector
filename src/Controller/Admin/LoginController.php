<?php

namespace Vector\Controller\Admin;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Vector\Module\Security\WebToken;
use Vector\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use PDO;

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
        $html = $this->template->render('login.html.twig', [
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
        $repository = new UserRepository();
        if (null !== ($email = $request->get('email')) && false !== filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (null !== ($user = $repository->getBy('email', $email, PDO::PARAM_STR))) {

                $password = $user->getPassword();
                if (hash('sha256', trim($request->get('password', ''))) === $password) {
                    $user->setLastLogin(time());
                    $user->save();

                    $cookie = new Cookie('Auth-Token', WebToken::generate([
                        'resource' => $user->getId(),
                        'scope' => 'write'
                    ], $request));

                    $cookie->withHttpOnly(true);
                    $cookie->withSecure(true);

                    $response = new RedirectResponse('/admin', Response::HTTP_FOUND);
                    $response->headers->setCookie($cookie);
                    return $response;
                }
            }
        }

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
