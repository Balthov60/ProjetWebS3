<?php

namespace DUT\Models;


class User
{
    protected $pseudo;
    protected $password;
    protected $firstname;
    protected $lastname;
    protected $mail;
    protected $isAdmin;

    /**
     * User constructor.
     *
     * @param $pseudo
     * @param $password
     * @param $firstname
     * @param $lastname
     * @param $mail
     * @param $isAdmin
     */
    public function __construct($pseudo, $password, $firstname, $lastname, $mail, $isAdmin)
    {
        $this->pseudo = $pseudo;
        $this->password = $password;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->mail = $mail;
        $this->isAdmin = $isAdmin;
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
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Check if user have Admin permissions.
     *
     * @return boolean
     */
    public function isAdmin() {
        return $this->isAdmin;
    }
}