<?php

namespace DUT\Services;

use Doctrine\ORM\EntityManager;
use DUT\Models\Commentary;
use DUT\Models\Post;
use Silex\Application;

class SQLServices
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * SQLServices constructor.
     *
     * @param $app Application
     */
    public function __construct($app)
    {
        $this->entityManager = $app["em"];
    }

    /**
     * @param $entity mixed
     *
     * @return void
     */
    public function addEntity($entity) {
        $this->entityManager->merge($entity);
        $this->entityManager->flush();
    }

    /**
     * @param $entity mixed
     *
     * @return void
     */
    public function removeModel($entity) {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /***************/
    /* User Linked */
    /***************/

    /**
     * @param $username
     * @param $password
     *
     * @return boolean
     */
    public function userExistWithCorrectPassword($username, $password) {
        $repository = $this->entityManager->getRepository("DUT\\Models\\User");
        $user = $repository->find($username);

        return (isset($user) && $user->getPassword() == $password);
    }

    /**
     * @param $username
     * @return bool
     *
     * @return boolean
     */
    public function userExist($username)
    {
        $repository = $this->entityManager->getRepository("DUT\\Models\\User");
        $user = $repository->find($username);
        return (isset($user));
    }


    public function isAdmin($username)
    {
        $repository = $this->entityManager->getRepository("DUT\\Models\\User");
        $user = $repository->find($username);
        return ($user->isAdmin() == 1);
    }

    public function mailExist($mail)
    {
        $repository = $this->entityManager->getRepository("DUT\\Models\\User");
        $user = $repository->findBy(["mail" => $mail]);
        return (isset($user));
    }

    /***************/
    /* Post Linked */
    /***************/

    /**
     * @param $idPost integer
     * @return Post
     */
    public function getPostById($idPost) {
        $repository = $this->entityManager->getRepository("DUT\\Models\\Post");
        return $repository->find($idPost);
    }

    /**
     * return all posts added by the user;
     *
     * @return Post[]
     */
    public function getAllPosts($orderBy) {
        $repository = $this->entityManager->getRepository("DUT\\Models\\Post");
        return $repository->findBy([], ["idPost" => $orderBy]);
    }

    /**
     * return 10 last posts added by the user;
     *
     * @return array(Post)
     */
    public function getLastPosts() {
        $repository = $this->entityManager->getRepository("DUT\\Models\\Post");
        return $repository->findBy([], ["idPost" => "DESC"], 10);
    }

    /**
     * @param $idPost integer
     *
     * @return void
     */
    public function removePost($idPost)
    {
        $repository = $this->entityManager->getRepository("DUT\\Models\\Post");
        $item = $repository->find($idPost);

        if (isset($item)) {
            $this->removeCommentaryFor($idPost);
            $this->removeModel($item);
        }
    }

    /*********************/
    /* Commentary Linked */
    /*********************/

    /**
     * @param $idPost integer
     * @return void
     */
    public function removeCommentaryFor($idPost)
    {
        /** @var Commentary $item */

        $repository = $this->entityManager->getRepository("DUT\\Models\\Commentary");
        $items = $repository->findBy(["idPost" => $idPost]);

        if (isset($items)) {
            foreach ($items as $item) {
                $this->removeReactionFor($idPost, $item->getIdCommentary());
                $this->entityManager->remove($item);
            }
            $this->entityManager->flush();
        }
    }

    /**
     * Remove Commentary matching idPost & idCommentary.
     *
     * @param $idPost
     * @param $idCommentary
     */
    public function removeCommentary($idPost, $idCommentary)
    {
        $repository = $this->entityManager->getRepository("DUT\\Models\\Commentary");
        $item = $repository->findOneBy(["idPost" => $idPost, "idCommentary" => $idCommentary]);

        if (isset($item)) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }
    }

    /**
     * return all commentary for a post.
     *
     * @param $idPost
     * @return array
     */
    public function getCommentaryForPost($idPost)
    {
        $repository = $this->entityManager->getRepository("DUT\\Models\\Commentary");
        $items = $repository->findBy(["idPost" => $idPost]);

        if (!isset($items)) {
            $items = [];
        }

        return $items;
    }

    /**
     * Get Commentary matching idPost & idCommentary.
     *
     * @param $idPost
     * @param $idCommentary
     * @return Commentary
     */
    public function getCommentary($idPost, $idCommentary)
    {
        $repository = $this->entityManager->getRepository("DUT\\Models\\Commentary");
        return $repository->findOneBy(["idPost" => $idPost, "idCommentary" => $idCommentary]);
    }

    public function addCommentary(Commentary $commentary)
    {
        $commentary->setIdCommentary($this->getMaxCommentaryIDFor($commentary->getIdPost()));
        $this->addEntity($commentary);
    }

    /**
     * @param $idPost
     *
     * @return integer
     */
    public function getMaxCommentaryIDFor($idPost)
    {
        /** @var Commentary $commentaries */

        $commentaries = $this->getCommentaryForPost($idPost);
        $maxID = 1;
        if (isset($commentaries)) {
            $lastCommentary =  $commentaries[sizeof($commentaries) - 1];
            $maxID = $lastCommentary->getIdCommentary() + 1;
        }

        return $maxID;
    }

    /*******************/
    /* Reaction Linked */
    /*******************/

    /**
     * @param $idPost integer
     * @param $idComment
     *
     * @return void
     */
    public function removeReactionFor($idPost, $idComment)
    {
        $repository = $this->entityManager->getRepository("DUT\\Models\\Reaction");
        $items = $repository->findBy(["idPost" => $idPost, "idComment" => $idComment]);

        if (isset($items)) {
            foreach ($items as $item) {
                $this->entityManager->remove($item);
            }
            $this->entityManager->flush();
        }
    }
}