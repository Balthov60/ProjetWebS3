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


    public function displayLoginPage(Application $app) {
        session_start();
        $isConnected = isset($_SESSION["user"]["isConnected"]) ? $_SESSION["user"]["isConnected"] : null;
        $isAdmin = isset($_SESSION["user"]["isAdmin"]) ? $_SESSION["user"]["isAdmin"] : null;

        $pseudo = (isset($_COOKIE['pseudo'])) ? $_COOKIE['pseudo'] : null;

        $html = $app['twig']->render('login-page.twig', ["errorMsg" => null,
                                                        "isConnected" => $isConnected,
                                                        "isAdmin" => $isAdmin,
                                                        "pseudo" => $pseudo]);
        return new Response($html);
    }

    public function displayLoginPageWithErrorMsg($error, Application $app)
    {
       if($error == "invalidID")
            $html = $app['twig']->render('login-page.twig', ["errorMsg" => "Nom d'utilisateur ou mot de passe invalide"]);

        return new Response($html);
    }

    public function checkRememberMe() {
        if (isset($_POST['remember-me'])) {
            setcookie("username", $_POST['username'], time() + (86400 * 30), '/');
        }
        else
        {
            setcookie("username", "", time() - 3600, '/');
        }
    }

    public function login(Request $request, Application $app) {
        $sqlService = new SQLServices($app);
        $pseudo = $request->get("pseudo");
        $password = $request->get("password");

        if(isset($pseudo) && isset($password)) {
            if ($sqlService->userExist($pseudo, $password))
            {
                session_start();
                $_SESSION['user']['isConnected'] = true;
                $_SESSION['user']['isAdmin'] = false;
                $_SESSION['user']['pseudo'] = $pseudo;

                $this->checkRememberMe();
                $url = $app['url_generator']->generate('home');
            }
            elseif ($sqlService->isAdmin($pseudo, $password))
            {
                session_start();
                $_SESSION['user']['isConnected'] = true;
                $_SESSION['user']['isAdmin'] = true;

                $this->checkRememberMe();
                $url = $app['url_generator']->generate('home');
            }
            else
            {
                $_SESSION['user']['isConnected'] = false;
                if (isset($_COOKIE['pseudo']) && strcmp($pseudo, $_COOKIE["pseudo"]) != 0)
                    setcookie("pseudo", "", time() - 3600, '/');

                $url = $app['url_generator']->generate('login', ["error" => "invalidID"]);

            }
        }
        else
        {
            $url = $app['url_generator']->generate('login');
        }
        return $app->redirect($url);
    }



    public function logout(Application $app)
    {
        session_start();
        $_SESSION["user"]["isConnected"] = false;
        $_SESSION["user"]["isAdmin"] = false;
        $url = $app["url_generator"]->generate("login");
        return $app->redirect($url);
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