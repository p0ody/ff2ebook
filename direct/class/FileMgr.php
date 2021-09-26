<?php
require_once __DIR__."/enums.php";
require_once __DIR__."/../../conf/config.php";

class FileMgr {
    private $path;
    private $filetype;

    public function find($id, $source, $filetype = FileType::DEFAULT) {
        if (!FileType::isValid($filetype)) {
            throw new Exception("Invalid file type.");
            return false;
        }
        $this->filetype = $filetype;

        $fileFound = $this->globNewerFile($id, $source, $filetype);

        // If no file found and filetype is epub
        if (!$fileFound) {
            switch($filetype) {
                case FileType::DEFAULT:
                    throw new Exception("File not found.");
                    return false;

                case FileType::MOBI:
                    $epub = $this->globNewerFile($id, $source, FileType::DEFAULT);
                    if (!$epub) {
                        throw new Exception("File not found.");
                        return false;
                    }
                    // Create mobi then recheck if mobi file exist.
                    $mobi = $this->createMobi($epub);
                    $fileFound = $this->globNewerFile($id, $source, $filetype);

                    if (!$mobi || !$fileFound) {
                        throw new Exception("Could not convert to mobi.");
                            return false;
                    }

                    break;
            }
        } 
        
        if (!$fileFound) {
            throw new Exception("File not found.");
            return false;
        }

        $this->path = $fileFound;
        return true;
    }

    public function getDownload() {
        if (!file_exists($this->path)) {
            throw new Exception("File not found.");
            return false;
        }

        $this->setHeaders();
        readfile($this->path);
    }

    private function fileNameBuilder($id, $source) {
        if (!isset($id) || !isset($source)) {
            throw new Exception("Missing information for finding file.");
            return false;
        }

        if (!Sources::isValid($source)) {
            throw new Exception("Invalid source.");
            return false;
        }

        return $source ."_". $id;
    }

    /**
     * @param int $id
     * @param string $source
     * @param string $filetype
     * @return string|bool
     */
    private function globNewerFile($id, $source, $filetype) {
        $filesFound = [];
        foreach(new GlobIterator(Config::ARCHIVE_DIR ."/". $this->fileNameBuilder($id, $source) ."*.". $filetype, FilesystemIterator::SKIP_DOTS) as $file) {
            array_push($filesFound, $file->getPathname());
        }
        if (count($filesFound) > 0) {
            rsort($filesFound, SORT_NATURAL); // reverse sort to get newer file at the top.
            return $filesFound[0];
        }
        
        return false;
    }

    /**
     * @return bool;
     */
    private function createMobi($path) {
        if (!file_exists(Config::KINDLEGEN_PATH)) {
            throw new Exception("Could not find kindlegen to convert to mobi.");
            return false;
        }
        exec(Config::KINDLEGEN_PATH ." ". $path ." 2>&1", $returnArray, $return);

        return $return === 1;
    }

    private function setHeaders() {
        $mime = false;

        switch($this->filetype) {
            case FileType::EPUB:
                $mime = "application/epub+zip";
                break;
            
            case FileType::MOBI:
                $mime = "application/x-mobipocket-ebook";
                break;
        }
        if (!$mime) {
            throw new Exception("Invalid file type.");
        }

        $infos = $this->getFicInfos();
        if (!$infos) {
            throw new Exception("An error occured while fetching title.");
            return false;
        }

        $filename = $infos["title"] ." - ". $infos["author"] .".". $this->filetype;
            

        header("Content-Type: ". $mime);	
        header("Content-Length: " . filesize($this->path));
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Transfer-Encoding: binary");
    }

    /**
     * @param string $path
     * @return array["title"|"author"]|bool
     */
    private function getFicInfos() {
        $zip = new ZipArchive;
        $res = $zip->open(str_replace(FileType::MOBI, FileType::DEFAULT, $this->path)); // replace mobi by epub to be able to open and fetch information.
        if ($res === false) {
            throw new Exception("Could not open ebook to fetch informations.");
            return false;
        }
    
        $file = $zip->getFromName('OEBPS/content.opf', 0, ZipArchive::FL_NOCASE);
    
        if (!$file) {
            return false;
        }
        
        $title = null;
        $author = null;
        $match = [];
        if (preg_match("/<dc:title>(.+?)<\/dc:title>.+<dc:creator opf:file-as=\"(.+?)\"/si", $file, $match) === 1) {
            $title = $match[1];
            $author = $match[2];
            global $pdo;
            if (strlen($title) > 100) {
                $title = "Invalid title";
            }
            if (strlen($author) > 100) {
                $author = "Invalid author";
            }
    
            return Array("title" => $title, "author" => $author);
        }
    
        return false;
    }
}