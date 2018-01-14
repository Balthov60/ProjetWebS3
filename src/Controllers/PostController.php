<?php
/**
 * Created by PhpStorm.
 * User: balth
 * Date: 11/01/2018
 * Time: 16:56
 */

namespace DUT\Controllers;


use Symfony\Component\HttpFoundation\Request;
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
        $posts = $sqlServices->getAllPosts("DESC");

        $html = $app['twig']->render('list-all-cards.twig', ['posts' => $posts, 'isAdmin' => true]);
        return new Response($html);
    }

    public function orderPostsBy(Request $request, Application $app)
    {
        if($request->get("orderBy") == "lastToOld")
            return $app->redirect($app['url_generator']->generate('allPosts'));
        else {
            $sqlServices = new SQLServices($app);
            $posts = $sqlServices->getAllPosts("ASC");

            $html = $app['twig']->render('list-all-cards.twig', ['posts' => $posts, 'isAdmin' => true]);
            return new Response($html);
        }
    }
}