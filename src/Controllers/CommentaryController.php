<?php

namespace DUT\Controllers;


use DUT\Models\Commentary;
use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CommentaryController
{
    /**
     * Add commentary & redirect to post page.
     *
     * @param Application $app
     * @return RedirectResponse
     */
    public function addCommentary(Application $app) {
        $sqlServices = new SQLServices($app);

        $date = getdate();
        $formattedDate = $date["year"]
                       . "-" . str_pad($date["mon"], 2, '0', STR_PAD_LEFT)
                       . "-" . str_pad($date["mday"], 2, '0', STR_PAD_LEFT);

        $sqlServices->addCommentary(new Commentary($_POST["postID"], null,
            $_SESSION['user']['username'], $_POST["content"], $formattedDate));

        return new RedirectResponse($app["url_generator"]->generate("{idPost}", ["idPost" => $_POST["postID"]]));
    }

    /**
     * Add commentary & redirect to post page.
     *
     * @param Application $app
     * @return RedirectResponse
     */
    public function updateCommentary(Application $app) {
        $sqlServices = new SQLServices($app);

        $date = getdate();
        $formattedDate = $date["year"]
            . "-" . str_pad($date["mon"], 2, '0', STR_PAD_LEFT)
            . "-" . str_pad($date["mday"], 2, '0', STR_PAD_LEFT);

        $sqlServices->addEntity(new Commentary($_POST["postID"], $_POST["commentaryID"],
            $_SESSION['user']['username'], $_POST["content"], $formattedDate));

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

    public function editCommentary(Application $app, $idCommentary, $idPost) {
        $sqlServices = new SQLServices($app);

        return new Response($app['twig']->render('edit-commentary.twig',
                 ['userInfo' => $_SESSION["user"],
                  'commentary' => $sqlServices->getCommentary($idPost, $idCommentary)]
        ));
    }
}