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
        $html = $app['twig']->render('login-page.twig', ["errorMsg" => null]);
        return new Response($html);
    }

    public function displayLoginPageWithErrorMsg($errorMsg, Application $app)
    {
        if($errorMsg == "emptyField")
            $html = $app['twig']->render('login-page.twig', ["errorMsg" => "Tous les champs doivent être remplis !"]);
        else if($errorMsg == "unknownUser")
            $html = $app['twig']->render('login-page.twig', ["errorMsg" => "Nom d'utilisateur ou mot de passe invalide"]);

        return new Response($html);
    }

    public function login(Request $request, Application $app) {
        $pseudo = htmlspecialchars($request->get('username', null));
        $password = htmlspecialchars($request->get('password', null));
        $rememberMe = $request->get('remember-me', null);

        $sqlServices = new SQLServices($app);

        if(is_null($pseudo) || is_null($password))
            $url = $app['url_generator']->generate('loginError', ["errorMsg" => $rememberMe]);

        else if (!$sqlServices->userExistWithCorrectPassword($pseudo, md5($password)))
            $url = $app['url_generator']->generate("loginError", ["errorMsg" => $rememberMe]);

        else
        {
            $_SESSION["user"]["pseudo"] = $pseudo;
            $_SESSION["user"]["password"] = $password;
            $_SESSION["user"]["connected"] = true;

            if($sqlServices->isAdmin($pseudo))
                $_SESSION["user"]["isAdmin"] = true;

            if(!is_null($rememberMe)) {
                setcookie("pseudo", $pseudo, time() + 31 * 24 * 3600,
                                        null, null, false, true);
                setcookie("password", $password, time() + 31 * 24 * 3600,
                                        null, null, false, true);
            }
            $url = $app['url_generator']->generate('home');
        }

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