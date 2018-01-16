<?php

namespace DUT\Controllers;

use DUT\Services\SQLServices;
use DUT\Models\User;
use Silex\Application;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

const _USERNAME_TOO_SMALL = "Le nom d'utilisateur est trop petit";
const _USERNAME_TOO_BIG = "Le nom d'utilisateur est trop grand";
const _MAIL_ALREADY_EXISTS = "Cette adresse mail est déjà associé à un compte ";
const _USERNAME_ALREADY_EXISTS = "Ce nom d'utilisateur est déjà associé à un compte ";
const _CONFIRMATION_PASSWORD = "La confirmation de mot de passe ne correspond pas au mot de passe";
const _PASSWORD_TOO_SMALL = "Le mot de passe est trop court";


class AuthController
{
    const LOGIN_ERROR = "Nom d'utilisateur ou mot de passe invalide";

    /**
     * @param Application $app
     * @return Response
     */
    public function displayLoginPage(Application $app) {
        $twigParameter = ["errorMsg" => null,
                          "userInfo" => $app["session"]->get("user"),
                          "usernameInCookie" => $this->getUsernameInCookie()];

        return new Response($app['twig']->render('login-page.twig', $twigParameter));
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function displayLoginPageWithErrorMsg(Application $app){
        $twigParameter = ["errorMsg" => AuthController::LOGIN_ERROR,
                          "userInfo" => $app["session"]->get("user"),
                          "usernameInCookie" => $this->getUsernameInCookie()];

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



    public function displaySubscribePage(Application $app) {
        session_start();
        $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => null,
                                                            "isConnected" => true,
                                                            "isAdmin" => true]);
        return new Response($html);
    }

    public function displaySubscribePageErrorMsg($errorMsg, Application $app)
    {
        if($errorMsg == "usernameTooSmall")
            $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => _USERNAME_TOO_SMALL, "userInfo" => $_SESSION["user"]]);

        elseif ($errorMsg == "usernameTooBig")
            $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => _USERNAME_TOO_BIG, "userInfo" => $_SESSION["user"]]);

        elseif ($errorMsg == "mailAlreadyExist")
            $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => _MAIL_ALREADY_EXISTS, "userInfo" => $_SESSION["user"]]);

        elseif ($errorMsg == "usernameAlreadyExist")
            $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => _USERNAME_ALREADY_EXISTS, "userInfo" => $_SESSION["user"]]);

        elseif ($errorMsg == "confirmationPassword")
            $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => _CONFIRMATION_PASSWORD, "userInfo" => $_SESSION["user"]]);

        elseif ($errorMsg == "passwordTooSmall")
            $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => _PASSWORD_TOO_SMALL, "userInfo" => $_SESSION["user"]]);

        return new Response($html);
    }

    public function subscribe(Request $request, Application $app) {
        if(isset($_POST['mail']) && isset($_POST['username']) &&
            isset($_POST['password']) && isset($_POST['password-confirmation']))
        {
            $this->initSession();

            /* Test if data format is valid */

            if (strlen($_POST['username']) < 6) {
                $url = $app["url_generator"]->generate("subscribeError", ["errorMsg" => "usernameTooSmall"]);
                return $app->redirect($url);
            }
            if (strlen($_POST['username']) > 16) {
                $url = $app["url_generator"]->generate("subscribeError", ["errorMsg" => "usernameTooBig"]);
                return $app->redirect($url);
            }

            /* Test if IDs are available */

            $dbHandler = new SQLServices($app);

            if ($dbHandler->mailExist($_POST['mail'])) {
                $url = $app["url_generator"]->generate("subscribeError", ["errorMsg" => "mailAlreadyExist"]);
                return $app->redirect($url);
            }
            if ($dbHandler->userExist($_POST['username'])) {
                $url = $app["url_generator"]->generate("subscribeError", ["errorMsg" => "usernameAlreadyExist"]);
                return $app->redirect($url);
            }

            /* Test if Password are valid */

            if (strcmp($_POST['password'], $_POST['password-confirmation']) != 0) {
                $url = $app["url_generator"]->generate("subscribeError", ["errorMsg" => "confirmationPassword"]);
                return $app->redirect($url);
            }
            if (strlen($_POST['password']) < 8) {
                $url = $app["url_generator"]->generate("subscribeError", ["errorMsg" => "passwordTooSmall"]);
                return $app->redirect($url);
            }

            /* If all test succeeded, create a new user */

            $dbHandler->addEntity(new User($_POST['username'],
                        md5($_POST['password']),
                            $_POST['firstname'],
                            $_POST['lastname'],
                            $_POST['mail'],
                            0));

            $url = $app["url_generator"]->generate("login");
        }
        else
        {
            session_destroy();
            session_start();
            $url = $app["url_generator"]->generate("home");
        }

        return $app->redirect($url);
    }

    private function initSession() {
        $_SESSION["form"]["mail"] = $_POST["mail"];
        $_SESSION["form"]["username"] = $_POST["username"];
    }


    /**
     * Check if user is connected and if not redirect him to login page.
     *
     * @param Request $request
     * @param Application $app
     * @return null|RedirectResponse
     */
    public static function isConnected(Request $request, Application $app) {
        if (!isset($_SESSION["user"]["connected"]) || $_SESSION["user"]["connected"] != true) {
            return new RedirectResponse($app["url_generator"]->generate("login"));
        }
        return null;
    }

    /**
     * Check if user is an admin and if not redirect him to login page.
     *
     * @return null|RedirectResponse
     */
    public function isAdmin() {
        if ($_SESSION["user"]["isAdmin"] != true) {
            return new RedirectResponse('../login');
        }
        return null;
    }

}