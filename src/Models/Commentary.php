<?php

namespace DUT\Models;


class Commentary
{
    protected $idPost;
    protected $idCommentary;
    protected $pseudo;
    protected $content;

    /**
     * Commentary constructor.
     *
     * @param $idPost
     * @param $idCommentary
     * @param $pseudo
     * @param $content
     */
    public function __construct($idPost, $idCommentary, $pseudo, $content)
    {
        $this->idPost = $idPost;
        $this->idCommentary = $idCommentary;
        $this->pseudo = $pseudo;
        $this->content = $content;
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
    public function getIdCommentary()
    {
        return $this->idCommentary;
    }

    /**
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }



}