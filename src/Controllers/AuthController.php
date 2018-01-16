<?php

namespace DUT\Controllers;

use DUT\Services\SQLServices;
use DUT\Models\User;
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

    // TODO: controller

    /**
     * @param Application $app
     * @return Response
     */
    public function displaySubscribePage(Application $app) {
        $twigParameter = ["errorMsg" => null, "userInfo" => $app["session"]->get("user"), "page" => "subscribe"];

        if (null === $app["session"]->get("subscribeForm"))
            $this->initEmptySubscribeSession($app);

        $twigParameter["subscribeForm"] = $app["session"]->get("subscribeForm");

        return new Response( $app['twig']->render('subscribe-page.twig',$twigParameter));
    }

    /**
     * @param Application $app
     * @param $errorMsg
     * @return Response
     */
    public function displaySubscribePageErrorMsg(Application $app, $errorMsg)
    {
        if($errorMsg == "usernameTooSmall")
            $twigParameter = ["errorMsg" => AuthController::USERNAME_TOO_SMALL];

        elseif ($errorMsg == "usernameTooBig")
            $twigParameter = ["errorMsg" => AuthController::USERNAME_TOO_BIG];

        elseif ($errorMsg == "mailAlreadyExist")
            $twigParameter = ["errorMsg" => AuthController::MAIL_ALREADY_EXISTS];

        elseif ($errorMsg == "usernameAlreadyExist")
            $twigParameter = ["errorMsg" => AuthController::USERNAME_ALREADY_EXISTS];

        elseif ($errorMsg == "confirmationPassword")
            $twigParameter = ["errorMsg" => AuthController::CONFIRMATION_PASSWORD];

        elseif ($errorMsg == "passwordTooSmall")
            $twigParameter = ["errorMsg" => AuthController::PASSWORD_TOO_SMALL];

        $twigParameter["userInfo"] = $app["session"]->get("user");
        if (null === $app["session"]->get("subscribeForm"))
            $this->initEmptySubscribeSession($app);

        $twigParameter["subscribeForm"] = $app["session"]->get("subscribeForm");
        return new Response($app['twig']->render('subscribe-page.twig', $twigParameter));
    }

    /**
     * @param Application $app
     * @return RedirectResponse
     */
    public function subscribe(Application $app) {
        if(isset($_POST['mail']) && isset($_POST['username']) &&
            isset($_POST['password']) && isset($_POST['password-confirmation']))
        {
            $this->updateSubscribeSession($app);

            /* Test if data format is valid */

            if (strlen($_POST['username']) < 6) {
                return new RedirectResponse($app["url_generator"]->generate("subscribeError",
                                                                            ["errorMsg" => "usernameTooSmall"]));
            }
            if (strlen($_POST['username']) > 16) {
                return new RedirectResponse($app["url_generator"]->generate("subscribeError",
                                                                            ["errorMsg" => "usernameTooBig"]));
            }

            /* Test if IDs are available */

            $SQLServices = new SQLServices($app);

            if ($SQLServices->userExist($_POST['username'])) {
                return new RedirectResponse($app["url_generator"]->generate("subscribeError",
                                                                            ["errorMsg" => "usernameAlreadyExist"]));
            }
            if ($SQLServices->mailExist($_POST['mail'])) {
                return new RedirectResponse($app["url_generator"]->generate("subscribeError",
                                                                            ["errorMsg" => "mailAlreadyExist"]));
            }

            /* Test if Password are valid */

            if (strlen($_POST['password']) < 8) {
                return new RedirectResponse($app["url_generator"]->generate("subscribeError",
                                                                            ["errorMsg" => "passwordTooSmall"]));
            }
            if (strcmp($_POST['password'], $_POST['password-confirmation']) != 0) {
                return new RedirectResponse($app["url_generator"]->generate("subscribeError",
                                                                            ["errorMsg" => "confirmationPassword"]));
            }

            /* If all test succeeded, create a new user */

            $SQLServices->addEntity(new User($_POST['username'],
                                             $_POST['password'],
                                             $_POST['firstname'],
                                             $_POST['lastname'],
                                             $_POST['mail'],
                                             0));

            return new RedirectResponse($app["url_generator"]->generate("login"));
        }

        return new RedirectResponse($app["url_generator"]->generate("home"));
    }

    /**
     * Init Subscribe with empty field.
     *
     * @param Application $app
     */
    private function initEmptySubscribeSession(Application $app) {
        $app["session"]->set("subscribeForm", ['username' => '', 'firstname' => '', 'lastname' => '', 'mail' => '']);
    }

    /**
     * Update subscribe with form fields.
     *
     * @param Application $app
     */
    private function updateSubscribeSession(Application $app) {
        $app["session"]->set("subscribeForm", ['username' => $_POST['username'], 'mail' => $_POST['mail'],
                                               'firstname' => $_POST['firstname'], 'lastname' => $_POST['lastname']]);
    }


    // MIDDLEWARE TEST


    /**
     * Check if user is connected and if not redirect him to login page.
     *
     * @param Application $app
     * @return null|RedirectResponse
     */
    public static function isConnected(Application $app) {
        if ($app["session"]->get("user")["isConnected"] != true) {
            return new RedirectResponse($app["url_generator"]->generate("login"));
        }
        return null;
    }

    /**
     * Check if user is an admin and if not redirect him to login page.
     *
     * @param Application $app
     * @return null|RedirectResponse
     */
    public function isAdmin(Application $app) {
        if ($app["session"]->get("user")["isAdmin"] != true) {
            return new RedirectResponse($app["url_generator"]->generate("login"));
        }
        return null;
    }

}