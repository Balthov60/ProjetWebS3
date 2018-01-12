<?php

namespace DUT\Models;

class Card
{
    protected $image;
    protected $text;

    public function __construct($image, $text)
    {
        $this->image = $image;
        $this->text = $text;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getText()
    {
        return $this->text;
    }
}