<?php

namespace DUT\Models;


class Reaction
{
    protected $idPost;
    protected $idReaction;
    protected $owner;
    protected $type;

    /**
     * Reaction constructor.
     *
     * @param $idPost
     * @param $idReaction
     * @param $owner
     * @param $type
     */
    public function __construct($idPost, $idReaction, $owner, $type)
    {
        $this->idPost = $idPost;
        $this->idReaction = $idReaction;
        $this->owner = $owner;
        $this->type = $type;
    }

    /**
     * @return integer
     */
    public function getIdPost()
    {
        return $this->idPost;
    }

    /**
     * @return integer
     */
    public function getIdReaction()
    {
        return $this->idReaction;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


}