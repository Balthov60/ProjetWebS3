<?php


namespace DUT\Controllers;


use DUT\Models\Post;
use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\BrowserKit\Response;

class HomeController
{

    public function displayHomePage(Application $app) {
        /** @var Post $post */

        $sqlServices = new SQLServices($app);

        $mainPost = $sqlServices->getPostById("1"); //TODO: Implement Handling of main Post in admin panel
        $posts = $sqlServices->getLastPosts();

        if (($key = array_search($mainPost, $posts)) !== false) {
            unset($posts[$key]);
        }

        if (sizeof($posts) % 2 != 1)
            array_pop($posts);

        $html = $app['twig']->render('home.twig', ['posts' => $posts, 'mainPost' => $mainPost]);
        return new Response($html);
    }

}