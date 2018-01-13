<?php

namespace DUT\Controllers;


use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class PostController
{
    public function displayPost(Application $app, $idPost) {
        $sqlService = new SQLServices($app);

        return new Response($app['twig']->render('post-page.twig', ['post' => $sqlService->getPostById($idPost)]));
    }

    public function displayPostEdition($idPost) {
        return new Response("Edit Post n$idPost");
    }
}