<?php

namespace Vector\Controller;

use Vector\Router;
use Vector\Module\Controller\FrontendController;
use Vector\Module\Transient\SqlTransient;
use Vector\Module\SqlClient;
use Vector\Module\Security\AuthToken;
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
    protected const MAX_HOURLY_ATTEMPS = 5;

    protected function register(): void
    {
        Router::route(['GET'], '^/login?$', [$this, 'loginViewAction']);
        Router::route(['POST'], '^/login/submit?$', [$this, 'loginSubmitAction']);
    }

    /**
     * Route '/login'
     * Twig template
     * @return Response
     */
    public function loginViewAction(Request $request): Response
    {

        /**
         * @var string $requestRef
         * @var SqlTransient $nonce
         * We set the login-nonce to handle brute force attacks.
         */
        $requestRef = $request->getClientIp() . '%' . $request->headers->get('User-Agent');
        $nonce = new SqlTransient('login-nonce-{' . $requestRef . '}');
        if (false === $nonce->isValid(HOUR_IN_SECONDS)) {
            $nonce->setData(0);
        }

        /**
         * @var string $html
         * We retrive view raw html from the twig template engine.
         */
        $html = $this->template->render('admin/login.html.twig', [
            'title' => 'Vector - Admin login',
            'description' => 'Vector administration area login.',
            'formMethod' => 'POST',
            'formAction' => '/login/submit'
        ]);

        return new Response($html, Response::HTTP_OK);
    }

    /**
     * Route '/login/submit'
     * Form action
     * @return RedirectResponse
     */
    public function loginSubmitAction(Request $request): RedirectResponse
    {

        /**
         * @var string $requestRef
         * @var SqlTransient $nonce
         * We build the login-nonce to handle brute force attacks.
         */
        $requestRef = $request->getClientIp() . '%' . $request->headers->get('User-Agent');
        $nonce = new SqlTransient('login-nonce-{' . $requestRef . '}');
        if (false === $nonce->isValid(HOUR_IN_SECONDS) or
            ($attempts = (int) $nonce->getData()) >= self::MAX_HOURLY_ATTEMPS) {
            return new RedirectResponse('/login', Response::HTTP_FOUND);
        } else {
            $attempts = $attempts + 1;
            $nonce->setData($attempts);
        }

        /**
         * @var string $email
         * @var string $password
         * @var array $result
         * If the provided email is valid we search for an existing database user.
         * On password match set the autentication cookie and redirect to /admin.
         */
        if (null !== ($email = $request->get('email'))) {
            if (false !== filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $sql = SqlClient::getInstance();
                $result = $sql->getResults("SELECT `ID`, `password` FROM `users` WHERE `email` = ? LIMIT 1", [
                    ['type' => 's', 'value' => $email]
                ]);
                if (true === $result['success'] and !empty($result['data'])) {
                    $password = $result['data']['password'];
                    if (hash('sha256', $request->get('password', '')) === $password) {
                        $authToken = new AuthToken([
                            'userId' => $result['data']['ID'],
                            'ipAddress' => $request->getClientIp(),
                            'userAgent' => $request->headers->get('User-Agent')
                        ]);
                        $cookie = new Cookie('Auth-Token', $authToken->generate());
                        $response = new RedirectResponse('/admin', Response::HTTP_FOUND);
                        $response->headers->setCookie($cookie);
                        return $response;
                    }
                }
            }
        }

        return new RedirectResponse('/login', Response::HTTP_FOUND);
    }

}
