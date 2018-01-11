<?php

namespace DUT\Services;

use Doctrine\ORM\EntityManager;
use DUT\Models\Commentary;
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
        $this->entityManager->persist($entity);
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
        $repository = $this->entityManager->getRepository("User");
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
        $repository = $this->entityManager->getRepository("User");
        $user = $repository->find($username);

        return (isset($user));
    }

    /***************/
    /* Post Linked */
    /***************/

    /**
     * @param $idPost integer
     *
     * @return void
     */
    public function removePost($idPost)
    {
        $repository = $this->entityManager->getRepository("Post");
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

        $repository = $this->entityManager->getRepository("Commentary");
        $items = $repository->findBy(["idPost" => $idPost]);

        if (isset($items)) {
            foreach ($items as $item) {
                $this->removeReactionFor($idPost, $item->getIdCommentary());
                $this->entityManager->remove($item);
            }
            $this->entityManager->flush();
        }
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
        $repository = $this->entityManager->getRepository("Reaction");
        $items = $repository->findBy(["idPost" => $idPost, "idComment" => $idComment]);

        if (isset($items)) {
            foreach ($items as $item) {
                $this->entityManager->remove($item);
            }
            $this->entityManager->flush();
        }
    }
}