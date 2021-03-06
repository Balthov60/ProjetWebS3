<?php

namespace DUT\Controllers;


use DUT\Models\DateUtils;
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
                           'commentaries' =>  $sqlService->getCommentaryForPost($idPost),
                           'page' => "post"];

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

        $twigParameter = ['post' => $sqlService->getPostById($idPost),
                          'userInfo' => $app['session']->get("user"),
                          'page' => "editPost"];
        return new Response($app['twig']->render('edit-post.twig', $twigParameter));
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function displayPostCreation(Application $app)
    {
        $twigParameter = ['post' => new Post("", "", "", "", ""),
                          'userInfo' => $app['session']->get("user"), 'page' => "editPost"];

        return new Response($app['twig']->render('edit-post.twig', $twigParameter));
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return RedirectResponse
     */
    public function savePost(Request $request, Application $app)
    {
        $sqlServices = new SQLServices($app);

        if(!is_null($request->files->get("picture"))) // Save Picture
        {
            $dir = $request->server->get('DOCUMENT_ROOT') . "/ProjetWebBlog/res/images";
            $pictureName = uniqid() . $request->get("picture_name");

            foreach ($request->files as $uploadedFile) {
                $uploadedFile->move($dir, $pictureName);
            }
        }
        else
        {
            $pictureName = $request->get("picture_name");
        }

        $idPost = ($request->get("postID") != "") ? $request->get("postID") : null;

        $sqlServices->addEntity(new Post($idPost, $request->get("title"), $request->get("content"),
                                         DateUtils::getFormattedCurrentDate(), $pictureName));

        return new RedirectResponse($app["url_generator"]->generate("home"));
    }

    /**
     * @param Application $app
     * @param $idPost
     * @return RedirectResponse
     */
    public function removePost(Application $app, $idPost) {
        $sqlServices = new SQLServices($app);
        $sqlServices->removePost($idPost);

        return new RedirectResponse($app["url_generator"]->generate("home"));
    }


    /* List Post Page */

    /**
     * Display a list of the articles.
     *
     * @param Application $app
     * @return Response
     */
    public function displayAllPosts(Application $app)
    {
        $sqlServices = new SQLServices($app);
        $posts = $sqlServices->getAllPosts("DESC");

        $twigParameter = ['posts' => $posts, 'userInfo' => $app["session"]->get("user"), 'page' => "posts"];
        return new Response($app['twig']->render('list-all-cards.twig', $twigParameter));
    }
}