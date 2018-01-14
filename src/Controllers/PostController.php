<?php
/**
 * Created by PhpStorm.
 * User: balth
 * Date: 11/01/2018
 * Time: 16:56
 */

namespace DUT\Controllers;


use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use DUT\Services\SQLServices;

class PostController
{
    public function displayPost($idPost) {
        return new Response("Post n$idPost");
    }

    public function displayPostEdition($idPost) {
        return new Response("Edit Post n$idPost");
    }

    public function displayAllPosts(Application $app)
    {
        $sqlServices = new SQLServices($app);
        $posts = $sqlServices->getAllPosts();

        $html = $app['twig']->render('list-all-cards.twig', ['posts' => $posts]);
        return new Response($html);
    }
}