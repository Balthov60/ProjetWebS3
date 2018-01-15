<?php

namespace DUT\Controllers;


use DUT\Models\Commentary;
use DUT\Models\Post;
use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
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

    public function displayPostCreation(Application $app)
    {
        return new Response($app['twig']->render('edit-post.twig',
            ['post' => new Post("", "", "", "", "")]));
    }

    public function savePost(Request $request, Application $app)
    {
        $dir = $request->server->get('DOCUMENT_ROOT') . "/res/images";

        foreach ($request->files as $uploadedFile) {
            $pictureName = uniqid() . "." . $uploadedFile->guessExtension();
            $uploadedFile->move($dir, $pictureName);
        }

        $sqlServices = new SQLServices($app);
        $sqlServices->addEntity(new Post(null,
            $request->get("title"),
            $request->get("content"),
            date("Y/m/d"),
            $pictureName));

        return $app->redirect($app["url_generator"]->generate("home"));

    }

    public function removePost(Application $app, $idPost) {
        $sqlServices = new SQLServices($app);
        $sqlServices->removePost($idPost);

        return new RedirectResponse($app["url_generator"]->generate("", $_SESSION["user"]));
    }

    public function displayAllPosts(Application $app)
    {
        session_start();
        $sqlServices = new SQLServices($app);
        $posts = $sqlServices->getAllPosts("DESC");

        $html = $app['twig']->render('list-all-cards.twig', ['posts' => $posts, 'isAdmin' => $_SESSION["user"]["isAdmin"]]);
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