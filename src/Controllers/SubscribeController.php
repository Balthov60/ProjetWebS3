<?php

namespace DUT\Controllers;


use DUT\Models\User;
use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SubscribeController
{
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
     * (used to keep user data even if he failed his subscription)
     *
     * @param Application $app
     */
    private function initEmptySubscribeSession(Application $app) {
        $app["session"]->set("subscribeForm", ['username' => '', 'firstname' => '', 'lastname' => '', 'mail' => '']);
    }

    /**
     * Update subscribe with form fields.
     * (used to keep user data even if he failed his subscription)
     *
     * @param Application $app
     */
    private function updateSubscribeSession(Application $app) {
        $app["session"]->set("subscribeForm", ['username' => $_POST['username'], 'mail' => $_POST['mail'],
            'firstname' => $_POST['firstname'], 'lastname' => $_POST['lastname']]);
    }

}