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
    protected function register(): void
    {
        Router::route(['GET'], '^/login?$', [$this, 'loginViewAction']);
        Router::route(['POST'], '^/login/submit?$', [$this, 'loginSubmitAction']);
    }

    /**
     * Route: '/login'
     * Methods: GET
     * @return Response
     */
    public function loginViewAction(Request $request): Response
    {

        /**
         * @var string $requestRef
         * @var SqlTransient $bfpt
         * We set the login-nonce to handle brute force attacks.
         */
        $requestRef = $request->getClientIp() . '%' . $request->headers->get('User-Agent');
        $bfpt = new SqlTransient('login-nonce-{' . $requestRef . '}');
        if (false === $bfpt->isValid(HOUR_IN_SECONDS)) {
            $bfpt->setData(0);
        }

        /**
         * @var string $html
         * We retrive view raw html from the twig template engine.
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
     * @return RedirectResponse
     */
    public function loginSubmitAction(Request $request): RedirectResponse
    {

        /**
         * @var object $config
         * @var string $requestRef
         * @var SqlTransient $bfpt
         * We build the login-nonce to handle brute force attacks.
         */
        global $config;
        $requestRef = $request->getClientIp() . '%' . $request->headers->get('User-Agent');
        $bfpt = new SqlTransient('login-nonce-{' . $requestRef . '}');
        if (false === $bfpt->isValid(HOUR_IN_SECONDS) or
            ($attempts = (int) $bfpt->getData()) >= $config->security->max_login_attempts) {
            return new RedirectResponse('/login?success=false', Response::HTTP_FOUND);
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

        /**
         * @var int $attempts
         * Increasing the attempts variable on failure.
         */
        $bfpt->setData($attempts + 1);
        return new RedirectResponse('/login?success=false', Response::HTTP_FOUND);

    }

}
