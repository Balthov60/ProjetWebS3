<?php

namespace DUT\Models;


class Commentary
{
    protected $idPost;
    protected $idCommentary;
    protected $pseudo;
    protected $content;
    protected $postDate;

    /**
     * Commentary constructor.
     *
     * @param $idPost
     * @param $idCommentary
     * @param $pseudo
     * @param $content
     * @param $postDate
     */
    public function __construct($idPost, $idCommentary, $pseudo, $content, $postDate)
    {
        $this->idPost = $idPost;
        $this->idCommentary = $idCommentary;
        $this->pseudo = $pseudo;
        $this->content = $content;
        $this->postDate = $postDate;
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

    /**
     * @return string (date YYYY/MM/DD)
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    public function setIdCommentary($idCommentary)
    {
        $this->idCommentary = $idCommentary;
    }
}