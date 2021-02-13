<?php

class Chapter
{
    private $text;
    private $title;
    private $chapNum;

    function __construct($chapNum, $title, $text)
    {
        $this->chapNum = $chapNum;
        $this->title = $title;
        $this->text = $text;

    }

    public function getChapNum() { return $this->chapNum; }
    public function getTitle() { return $this->title; }
    public function getText() { return $this->text; }
}