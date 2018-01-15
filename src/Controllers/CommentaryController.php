<?php

namespace DUT\Controllers;


use DUT\Models\Commentary;
use DUT\Services\SQLServices;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CommentaryController
{
    /**
     * Add commentary & redirect to post page
     *
     * @param Application $app
     * @return RedirectResponse
     */
    public function addCommentary(Application $app) {
        $sqlServices = new SQLServices($app);

        $date = getdate();
        $formattedDate = $date["year"] . "-" . $date["mon"] . " -" . $date["mday"];

        $sqlServices->addCommentary(new Commentary($_POST["postID"], null,
            $_SESSION['user']['username'], $_POST["content"], $formattedDate));

        return new RedirectResponse($app["url_generator"]->generate("{idPost}", ["idPost" => $_POST["postID"]]));
    }

    public function removeCommentary(Application $app, $idCommentary, $idPost) {
        $sqlServices = new SQLServices($app);
        $sqlServices->removeCommentary($idPost, $idCommentary);

        return new RedirectResponse($app["url_generator"]->generate("{idPost}", ["idPost" => $idPost]));
    }
}