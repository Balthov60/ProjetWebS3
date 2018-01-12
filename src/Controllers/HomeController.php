<?php


namespace DUT\Controllers;


use Silex\Application;
use Symfony\Component\BrowserKit\Response;

class HomeController
{

    public function displayHomePage(Application $app) {
        return new Response($app['twig']->render('layout.twig'));
    }

}