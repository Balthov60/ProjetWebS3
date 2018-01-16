<?php

namespace DUT\Controllers;

use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class AuthController
{
    const LOGIN_ERROR = "Nom d'utilisateur ou mot de passe invalide";

    const USERNAME_TOO_SMALL = "Le nom d'utilisateur est trop petit";
    const USERNAME_TOO_BIG = "Le nom d'utilisateur est trop grand";
    const MAIL_ALREADY_EXISTS = "Cette adresse mail est déjà associée à un compte ";
    const USERNAME_ALREADY_EXISTS = "Ce nom d'utilisateur est déjà associé à un compte ";
    const CONFIRMATION_PASSWORD = "La confirmation de mot de passe ne correspond pas au mot de passe";
    const PASSWORD_TOO_SMALL = "Le mot de passe est trop court";

    /**
     * @param Application $app
     * @return Response
     */
    public function displayLoginPage(Application $app) {
        $twigParameter = ["errorMsg" => null,
                          "userInfo" => $app["session"]->get("user"),
                          "usernameInCookie" => $this->getUsernameInCookie(),
                          "page" => "auth"];

        return new Response($app['twig']->render('login-page.twig', $twigParameter));
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function displayLoginPageWithErrorMsg(Application $app){
        $twigParameter = ["errorMsg" => AuthController::LOGIN_ERROR,
                          "userInfo" => $app["session"]->get("user"),
                          "usernameInCookie" => $this->getUsernameInCookie(),
                          "page" => "auth"];

        return new Response($app['twig']->render('login-page.twig', $twigParameter));
    }

    /**
     * @return string : username if exist in cookie, empty elsewhere.
     */
    private function getUsernameInCookie()
    {
        return (isset($_COOKIE['username'])) ? $_COOKIE['username'] : "";
    }

    /**
     * Check if "Remember-me" had been checked and update cookies.
     * @param $username
     */
    private function updateCookies($username) {
        if (isset($_POST['remember-me'])) {
            setcookie("username", $username, time() + (86400 * 30), '/');
        }
        else
        {
            setcookie("username", "", time() - 3600, '/');
        }
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return RedirectResponse
     */
    public function login(Request $request, Application $app) {
        $sqlService = new SQLServices($app);
        $username = $request->get("username");
        $password = $request->get("password");

        if(isset($username) && isset($password)) {
            if ($sqlService->userExistWithCorrectPassword($username, $password))
            {
                $app["session"]->set("user", ["isConnected" => true,
                                              "username" => $username,
                                              "isAdmin" => $sqlService->isAdmin($username)]);
                $this->updateCookies($username);
                return new RedirectResponse($app['url_generator']->generate('home'));
            }
            else
            {
                // Remove Cookies if user try to log with another account.
                if (isset($_COOKIE['pseudo']) && strcmp($username, $_COOKIE["pseudo"]) != 0)
                    setcookie("pseudo", "", time() - 3600, '/');
            }
        }
        return new RedirectResponse($app['url_generator']->generate('loginError'));
    }

    /**
     * @param Application $app
     * @return RedirectResponse
     */
    public function logout(Application $app)
    {
        $app["session"]->set("user", ["username" => "", "isConnected" => false, "isAdmin" => false]);

        return new RedirectResponse($app["url_generator"]->generate("home"));
    }



    /**
     * Check if user is connected and if not redirect him to login page.
     *
     * @param Request $request
     * @param Application $app
     * @return null|RedirectResponse
     */
    public static function isConnected(Request $request, Application $app) {
        if ($app["session"]->get("user")["isConnected"] != true) {
            return new RedirectResponse($app["url_generator"]->generate("login"));
        }
        return null;
    }

    /**
     * Check if user is an admin and if not redirect him to login page.
     *
     * @param Request $request
     * @param Application $app
     * @return null|RedirectResponse
     * @internal param Request $request
     */
    public static function isAdmin(Request $request, Application $app) {
        if ($app["session"]->get("user")["isAdmin"] != true) {
            return new RedirectResponse($app["url_generator"]->generate("login"));
        }
        return null;
    }

    /**
     * Check if user is an admin or if he is owner of the commentary and if not redirect him to login page.
     * If commentary doesn't exist redirect him to home page.
     *
     * @param Request $request
     * @param Application $app
     * @return null|RedirectResponse
     * @internal param Request $request
     */
    public static function isOwnerOrAdmin(Request $request, Application $app) {
        $sqlServices = new SQLServices($app);
        $commentary = $sqlServices->getCommentary($request->get("idPost"), $request->get("idCommentary"));

        if ($commentary == null)
            return new RedirectResponse($app["url_generator"]->generate("home"));

        if ($app["session"]->get("user")["isAdmin"] != true &&
            $commentary->getPseudo() != $app["session"]->get("user")["username"])
        {
            return new RedirectResponse($app["url_generator"]->generate("login"));
        }
        return null;
    }

}