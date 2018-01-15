<?php

namespace DUT\Controllers;


use DUT\Models\Commentary;
use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PostController
{
    public function displayPost(Application $app, $idPost) {
        $sqlService = new SQLServices($app);

        return new Response($app['twig']->render('post-page.twig',
                                                ['userInfo' => $_SESSION["user"],
                                                 'post' => $sqlService->getPostById($idPost),
                                                 'commentaries' =>  $sqlService->getCommentaryForPost($idPost)]));
    }

    public function displayPostEdition(Application $app, $idPost) {
        $sqlService = new SQLServices($app);

        return new Response($app['twig']->render('edit-post.twig',
                            ['post' => $sqlService->getPostById($idPost)]));
    }

    public function removePost(Application $app, $idPost) {
        $sqlServices = new SQLServices($app);
        $sqlServices->removePost($idPost);

        return new RedirectResponse($app["url_generator"]->generate("", $_SESSION["user"]));
    }
}