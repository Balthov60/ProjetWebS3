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
        $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => null]);
        return new Response($html);
    }

    public function displaySubscribePageErrorMsg($errorMsg, Application $app)
    {
        if($errorMsg == "emptyField")
            $html = $app['twig']->render('suscribe-page.twig', ["errorMsg" => "Tous les champs doivent être remplis!"]);
        return new Response($html);
    }

    public function subscribe(Request $request, Application $app) {
        $firstname = htmlspecialchars($request->get('firstname', null));
        $lastname = htmlspecialchars($request->get('lastname', null));
        $pseudo = htmlspecialchars($request->get('username', null));
        $password = htmlspecialchars($request->get('password', null));
        $mail = htmlspecialchars($request->get('mail', null));

        $user = new User($pseudo, md5($password), $firstname, $lastname, $mail, 0);
        $sqlService = new SQLServices($app);

        if($sqlService->userExist($user->getPseudo()))
            $url = $app['url_generator']->generate('subscribeError', ["errorMsg" => "emptyField"]);

        else
        {
            $sqlService->addEntity($user);
            $url = $app['url_generator']->generate('login');
        }

        return $app->redirect($url);
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
        if (isset($_SESSION["user"]["connected"]) && $_SESSION["user"]["isAdmin"] != true) {
            return new RedirectResponse('../login');
        }
        return null;
    }

}