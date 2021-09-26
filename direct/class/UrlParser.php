<?php
require_once __DIR__."/enums.php";

// Example URL: http://www.ff2ebook.com/old/ffn-bot/index.php?id=2318355&source=ff&filetype=epub

class UrlParser
{
    private $id, $source, $fileType;


    /**
    * @param array $GET Pass the complete $GET to this constructor
    */
    function __construct($GET) {
        if (!isset($GET["id"])) {
            throw new Exception("Missing ID.");
        }

        if (!isset($GET["source"])) {
            throw new Exception("Missing source.");
        }
        $GET["source"] = Sources::update($GET["source"]);

        if (!isset($GET["filetype"])) {
            $GET["filetype"] = FileType::DEFAULT;
        }
        
        if ($this->isIdValid($GET["id"])) {
            $this->setId($GET["id"]);
        }
        else {
            throw new Exception("Invalid ID.");
        }
        
        if ($this->isSourceValid($GET["source"])) {
            $this->setSource($GET["source"]);
        }
        else {
            throw new Exception("Invalid source.");
        }

        if ($this->isFileTypeValid($GET["filetype"])) {
            $this->setFileType($GET["filetype"]);
        }
        else {
            throw new Exception("Invalid filetype.");
        }
    }

    // ID
    private function isIdValid($newId) {
        return (is_numeric($newId) && strlen($newId) > 0);
    }

    private function setId(int $newId) {
        if (isset($newId)) {
            $this->id = $newId;
            return true;
        } 
        else {
            throw new Exception("Tried to assign empty ID");
        }   

        return false;
    }

    // Source
    private function isSourceValid($newSource) {
        return (strlen($newSource) > 0 && Sources::isValid($newSource));
    }

    private function setSource(string $newSource) {
        if (isset($newSource)) {
            $this->source = $newSource;
            return true;
        } 
        else {
            throw new Exception("Tried to assign empty source");
        }   

        return false;
    }

    // FileType
    private function isFileTypeValid($newType) {
        return (strlen($newType) > 0 && FileType::isValid($newType));
    }

    private function setFileType(string $newType) {
        if (isset($newType)) {
            $this->fileType = $newType;
        } 
        else {
            $this->fileType = FileType::DEFAULT;
        }   
        return true;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getSource() {
        return $this->source;
    }

    public function getFileType() {
        return $this->fileType;
    }
}