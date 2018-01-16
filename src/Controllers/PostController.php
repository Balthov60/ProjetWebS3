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
    /**
     * @param Application $app
     * @param $idPost
     * @return Response
     */
    public function displayPost(Application $app, $idPost) {
        $sqlService = new SQLServices($app);
        $twigParameters = ['userInfo' => $app['session']->get("user"),
                           'post' => $sqlService->getPostById($idPost),
                           'commentaries' =>  $sqlService->getCommentaryForPost($idPost)];

        return new Response($app['twig']->render('post-page.twig', $twigParameters));
    }

    /**
     * @param Application $app
     * @param $idPost
     * @return Response
     */
    public function displayPostEdition(Application $app, $idPost) {
        $sqlService = new SQLServices($app);
        $_SESSION["editedPostID"] = $idPost;
        return new Response($app['twig']->render('edit-post.twig',
                            ['post' => $sqlService->getPostById($idPost)]));
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function displayPostCreation(Application $app)
    {
        return new Response($app['twig']->render('edit-post.twig',
            ['post' => new Post("", "", "", "", "")]));
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return RedirectResponse
     */
    public function savePost(Request $request, Application $app)
    {
        session_start();
        $sqlServices = new SQLServices($app);
        if(!is_null($request->files->get("picture"))) {
            $dir = $request->server->get('DOCUMENT_ROOT') . "/res/images";
            $pictureName = uniqid() . $request->get("picture_name");

            foreach ($request->files as $uploadedFile)
                $uploadedFile->move($dir, $pictureName);
        }

        else
            $pictureName = $request->get("picture_name");

        if(isset($_SESSION["editedPostID"]) && !is_null($_SESSION["editedPostID"]))
        {
            $idPost = $_SESSION["editedPostID"];
            $_SESSION["editedPostID"] = null;
        }

        else
            $idPost = null;

        $sqlServices->addEntity(new Post($idPost,
            $request->get("title"),
            $request->get("content"),
            date("Y/m/d"),
            $pictureName));

        return $app->redirect($app["url_generator"]->generate("home"));

    }

    /**
     * @param Application $app
     * @param $idPost
     * @return RedirectResponse
     */
    public function removePost(Application $app, $idPost) {
        $sqlServices = new SQLServices($app);
        $sqlServices->removePost($idPost);

        return new RedirectResponse($app["url_generator"]->generate("", $_SESSION["user"]));
    }


    /* List Post Page */

    /**
     * @param Application $app
     * @return Response
     */
    public function displayAllPosts(Application $app)
    {
        $sqlServices = new SQLServices($app);
        $posts = $sqlServices->getAllPosts("DESC");

        $twigParameter = ['posts' => $posts, 'userInfo' => $app["session"]->get("user")];
        return new Response($app['twig']->render('list-all-cards.twig', $twigParameter));
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return RedirectResponse|Response
     */
    public function orderPostsBy(Request $request, Application $app)
    {
        if ($request->get("orderBy") == "lastToOld")
        {
            return $app->redirect($app['url_generator']->generate('allPosts'));
        }
        else
        {
            $sqlServices = new SQLServices($app);
            $posts = $sqlServices->getAllPosts("ASC");

            $twigParameter = ['posts' => $posts, 'userInfo' => $app["session"]->get("user")];
            return new Response($app['twig']->render('list-all-cards.twig', $twigParameter));
        }
    }
}