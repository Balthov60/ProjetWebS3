<?php
namespace DUT\controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

class TestController
{
    public function displayHomepage(Application $application)
    {
        $cards = [1,2,3];
        $html = $application['twig']->render('home.twig', ['cards' => $cards]);
        return new Response($html);
    }
}