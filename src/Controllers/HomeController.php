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
        session_start();

        $_SESSION["user"]["connected"] = true;
        $_SESSION["user"]["isAdmin"] = true;

        $isConnected = (isset($_SESSION["user"]["connected"])) ? true : false;
        $isAdmin = (isset($_SESSION["user"]["isAdmin"])) ? true : false;
        $sqlServices = new SQLServices($app);

        $mainPost = $sqlServices->getPostById("1"); //TODO: Implement Handling of main Post in admin panel
        $posts = $sqlServices->getLastPosts();

        if (($key = array_search($mainPost, $posts)) !== false) {
            unset($posts[$key]);
        }

        if (sizeof($posts) % 2 != 1)
            array_pop($posts);

        $html = $app['twig']->render('home.twig', ['posts' => $posts,
                                                    'mainPost' => $mainPost,
                                                    'isConnected' => $isConnected,
                                                    'isAdmin' => $isAdmin]);
        return new Response($html);
    }

}