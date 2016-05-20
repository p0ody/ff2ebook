<?php
require_once("class.FileManager.php");
require_once("class.ZipManager.php");

class Epub
{
    private $filepath;
    private $sourceDir;
    private $fm;
    private $zip;
    private $uuid;
    private $filesList;
    private $filesReady;


    function __construct($sourceDir, $filepath) // use realpath()
    {
        $this->filepath = $filepath;
        $this->sourceDir = $sourceDir;

        $this->opf = false;
        $this->toc = false;

        $this->fm = new FileManager();
        $this->zip = new ZipManager();

        $this->fm->createDirectory(dirname($this->filepath));

        $this->uuid = $this->gen_uuid();

        $this->filesList = Array();

        $this->error = new ErrorHandler();

        $this->filesReady = $this->prepareFiles();
    }

    private function prepareFiles()
    {
        if (!is_dir($this->sourceDir ."/OEBPS/Content"))
            return false;

        $itr = new DirectoryIterator($this->sourceDir ."/OEBPS/Content");
        foreach($itr as $file)
        {
            if (!$file->isFile())
                continue;

            if ($file->getBasename() == "title.xtml")
                $this->filesList[0] = $file->getPathname();

            $this->filesList[intval($file->getBasename(".xhtml"))] = $file->getPathname();
        }

        ksort($this->filesList, SORT_NUMERIC);

        $this->filesReady = true;
        return true;
    }

    public function create()
    {
        /*var_dump($this->filesReady);
        var_dump($this->generateOPF());
        var_dump($this->generateTOC());*/

        if (!$this->filesReady || !$this->generateOPF() || !$this->generateTOC())
            return false;

        return $this->createEpub();
    }

    private function generateOPF()
    {
        if (!$this->filesReady)
            return false;

        $blanks = file_get_contents("../blanks/epub/content.opf");
        if (!file_exists($this->sourceDir ."/OEBPS/Content/title.xhtml"))
            return false;;

        $infos = file_get_contents($this->sourceDir ."/OEBPS/Content/title.xhtml");

        $title = "";
        $author = "";
        $summary = "";
        if (preg_match("#<div class=\"fic-title\"><a.+?>(.+?)</a></div>.*?<div class=\"fic-author\">.*?By: <a.+?>(.+?)</a></div>.*?Summary:</span> (.+?)<br />#si", $infos, $match) === 1) {
            $title = $match[1];
            $author = $match[2];
            $summary = $match[3];
        }
        else
            return false;


        $manifest = "";
        $spine = "";
        foreach($this->filesList as $num => $line)
        {
            if ($num === 0)
                continue;

            $manifest .= "        <item id=\"chap" . $num ."\" href=\"Content/" . basename($line) . "\" media-type=\"application/xhtml+xml\" />" . PHP_EOL;
            $spine .= "        <itemref idref=\"chap" . $num . "\" />" . PHP_EOL;
        }

        $content = str_replace(
            Array("%title%", "%author%", "%uuid%", "%chapManifest%", "%summary%", "%chapSpine%"),
            Array($title, $author, $this->uuid, $manifest, $summary, $spine),
            $blanks);


        $file = fopen($this->sourceDir . "/OEBPS/content.opf", "w");
        fwrite($file, $content);
        fclose($file);

        return true;
    }

    private function generateTOC()
    {
        if (!$this->filesReady)
            return false;

        $blanks = file_get_contents("../blanks/epub/toc.ncx");

        if (!file_exists($this->sourceDir ."/OEBPS/Content/title.xhtml"))
            return false;;

        $infos = file_get_contents($this->sourceDir ."/OEBPS/Content/title.xhtml");

        $title = "";
        if (preg_match("#<div class=\"fic-title\">(.+?)</div>#si", $infos, $match) === 1) {
            $title = $match[1];
        } else
            return false;


        $toc = "";

        foreach($this->filesList as $num => $line)
        {
            if ($num === 0)
                continue;

            $chapter = file_get_contents($line);
            $chapTitle = "";
            if (preg_match("#<title>(.+?)</title>#si", $chapter, $match) === 1)
                $chapTitle = $match[1];
            else
                $chapTitle = "Chapter " . $num;

            $toc .= "		<navPoint id=\"navPoint-". ($num + 1) ."\" playOrder=\"". ($num + 1) ."\">";
            $toc .= "<navLabel><text>". $num .". ". $chapTitle . "</text></navLabel>";
            $toc .= "<content src=\"Content/" . basename($line) . "\"/>";
            $toc .= "</navPoint>" . PHP_EOL;
        }

        $content = str_replace(
            Array("%title%", "%uuid%", "%chapNav%"),
            Array($title, $this->uuid, $toc),
            $blanks);

        try {
            $file = fopen($this->sourceDir . "/OEBPS/toc.ncx", "w");
            fwrite($file, $content);
            fclose($file);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    private function gen_uuid()
    {
        return "urn:uuid:". sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function createEpub()
    {
        $z = $this->zip;
        if(!extension_loaded('zip'))
            return false;

        try
        {
            if (file_exists($this->filepath))
                $this->fm->deleteFile($this->filepath);

            copy("../blanks/epub/blank.epub", $this->filepath);

            $z->openZip($this->filepath);
            $z->addAllFileInDir($this->sourceDir ."/OEBPS", "OEBPS");
            $z->addAllFileInDir($this->sourceDir ."/OEBPS/Content", "OEBPS/Content");
            $z->addFile("../blanks/style.css", "OEBPS/Styles");
            $z->close();

        }
        catch(Exception $e)
        {
            return false;
        }

        return true;
    }

    public function getFilename() { return basename($this->filepath); }
}



/*
    Structure:
    mimetype
    META-INF/
        container.xml
    OEBPS/
        content.opf
        toc.ncx
        Content/
            title.xhtml
            1.xhtml
            2.xhtml
            ...
        Styles/
            style.css
 */