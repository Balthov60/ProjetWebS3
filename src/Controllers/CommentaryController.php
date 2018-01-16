<?php

namespace DUT\Controllers;

use DUT\Models\Commentary;
use DUT\Models\DateUtils;
use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


class CommentaryController
{
    /**
     * @param Application $app
     * @param $idCommentary
     * @param $idPost
     * @return Response
     */
    public function displayEditCommentaryPage(Application $app, $idCommentary, $idPost) {
        $sqlServices = new SQLServices($app);

        $twigParameters = ['userInfo' => $app['session']->get("user"),
                           'commentary' => $sqlServices->getCommentary($idPost, $idCommentary),
                           'page' => "commentary"];
        return new Response($app['twig']->render('edit-commentary.twig', $twigParameters));
    }

    /**
     * Add commentary & redirect to post page.
     *
     * @param Application $app
     * @return RedirectResponse
     */
    public function addCommentary(Application $app) {
        $sqlServices = new SQLServices($app);

        $sqlServices->addCommentary(new Commentary($_POST["postID"], null,
                                                   $app['session']->get("user")['username'],
                                                   $_POST["content"], DateUtils::getFormattedCurrentDate()));

        return new RedirectResponse($app["url_generator"]->generate("{idPost}", ["idPost" => $_POST["postID"]]));
    }

    /**
     * update commentary & redirect to post page.
     *
     * @param Application $app
     * @return RedirectResponse
     */
    public function updateCommentary(Application $app) {
        $sqlServices = new SQLServices($app);

        $sqlServices->addEntity(new Commentary($_POST["postID"], $_POST["commentaryID"],
                                               $app['session']->get("user")['username'],
                                               $_POST["content"], DateUtils::getFormattedCurrentDate()));

        return new RedirectResponse($app["url_generator"]->generate("{idPost}", ["idPost" => $_POST["postID"]]));
    }

    /**
     * Remove commentary & redirect to post page.
     *
     * @param Application $app
     * @param $idCommentary
     * @param $idPost
     * @return RedirectResponse
     */
    public function removeCommentary(Application $app, $idCommentary, $idPost) {
        $sqlServices = new SQLServices($app);
        $sqlServices->removeCommentary($idPost, $idCommentary);

        return new RedirectResponse($app["url_generator"]->generate("{idPost}", ["idPost" => $idPost]));
    }
}