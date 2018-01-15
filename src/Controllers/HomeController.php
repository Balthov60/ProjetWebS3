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
        $posts = $this->getPostList($sqlServices, $mainPost);

        $twigParameters = ['mainPost' => $mainPost, 'posts' => $posts, 'userInfo' => $_SESSION["user"]];
        return new Response($app['twig']->render('home-page.twig',$twigParameters));
    }

    /**
     * Return a list of the last posts (9max) create on the blog.
     * Remove the mainPost if in list.
     * Because of design requirement, remove the last if the list is pair. //TODO: Traduction pair.
     *
     * @param $sqlServices SQLServices
     * @param $mainPost Post
     * @return array(Post)
     */
    private function getPostList($sqlServices, $mainPost) {
        $posts = $sqlServices->getLastPosts();

        if (($key = array_search($mainPost, $posts)) !== false) {
            unset($posts[$key]);
        }

        if (sizeof($posts) % 2 != 1)
            array_pop($posts);

        return $posts;
    }

}