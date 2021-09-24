<?php

class DownloadFile
{
    private $filename, $title, $author;

    function __construct($filename, $title, $author)
    {
        $this->filename = $filename;
        $this->title = $title;
        $this->author = $author;

    }

    public function getFilename() { return $this->filename; }
    public function getTitle() { return $this->title; }
    public function getAuthor() { return $this->author; }

}