<?php
require_once("class.Chapter.php");

class FileManager
{
    function __construct()
    {

    }

    public function createOutputDir()
    {
        $time = microtime(true);
        $this->createDirectory("../output/". $time) or die();

        return $time;
    }

    /** @var Chapter $chapter */
    public function createChapterFile($dir, $chapter)
    {
        $this->createDirectory("../output/". $dir);

        $filename = "../output/$dir/". $chapter->getChapNum() .".xhtml";
        if (file_exists($filename))
            $this->deleteFile($filename);

        $blanks = file_get_contents("../blanks/chapter.xhtml");
        $xhtml = str_replace(Array("%title%", "%body%", "%chapNum%"), Array($chapter->getTitle(), $chapter->getText(), $chapter->getChapNum()), $blanks);

        $options = Array(
            "output-xhtml"  => true,
            "clean"         => true,
            "indent"        => 2,
            "indent-spaces" => 2,
            "wrap"          => 90,
            "char-encoding" => "utf8",
            "doctype"       => "omit"
        );

        $tidy = new Tidy();
        $tidy->parseString($xhtml, $options, "UTF8");
        $tidy->cleanRepair();

        $file = fopen($filename, "w");
        fwrite($file, "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\" ?>". PHP_EOL);
        fwrite($file, "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">". PHP_EOL);
        fwrite($file, $tidy);
        fclose($file);

        return $filename;
    }

    /** @var BaseHandler $fic */
    public function createTitlePage($dir, $fic)
    {
        $this->createDirectory("../output/". $dir);
        $filename = "../output/$dir/title.xhtml";

        if (file_exists($filename))
            $this->deleteFile($filename);

        $replacedBy = Array(
            "<a href=\"". $fic->getRealURL() ."\">". htmlentities($fic->getTitle()) ."</a>",
            "<a href=\"". $fic->getAuthorProfile() ."\">". htmlentities($fic->getAuthor()) ."</a>",
            !$fic->getFandom() ? "" : "<span class=\"bold\">Fandom:</span> ". $fic->getFandom() ."<br /><br />",
            !$fic->getSummary() ? "" : "<span class=\"bold\">Summary:</span> ". htmlentities($fic->getSummary()) ."<br /><br />",
            !$fic->getFicType() ? "" : "<span class=\"bold\">Fic type:</span> ". $fic->getFicType() ."<br /><br />",
            !$fic->getPublishedDate() ? "" : "<span class=\"bold\">Published:</span> ". date("Y-m-d", $fic->getPublishedDate()) ."<br /><br />",
            !$fic->getUpdatedDate() ? "" : "<span class=\"bold\">Last updated:</span> ". date("Y-m-d", $fic->getUpdatedDate()) ."<br /><br />",
            !$fic->getWordsCount() ? "" : "<span class=\"bold\">Words count:</span> ". $fic->getWordsCount() ."<br /><br />",
            !$fic->getaddInfos() ? "" : "<span class=\"bold\">Additional infos:</span> ". htmlentities($fic->getAddInfos()) ."<br /><br />",
            !$fic->getChapCount() ? "" : "<span class=\"bold\">Chapters count:</span> ". $fic->getChapCount() ."<br /><br />",
            date("Y-m-d", time()),
            !$fic->getCompleted() ? "" : "<span class=\"bold\">Status:</span> ". $fic->getCompleted() ."<br /><br />"
        );

        $blanks = file_get_contents("../blanks/title.xhtml");
        $xhtml = str_replace(
            Array(
                "%title%",
                "%author%",
                "%fandom%",
                "%summary%",
                "%ficType%",
                "%published%",
                "%updated%",
                "%wordsCount%",
                "%addInfos%",
                "%chapCount%",
                "%convertDate%",
                "%completed%"),
            $replacedBy, $blanks);


        $file = fopen($filename, "w");
        fwrite($file, $xhtml);
        fclose($file);

        return $filename;
    }

    public function createDirectory($dir)
    {
        if (file_exists($dir))
            return true;

        return mkdir($dir, 0777, true);
    }

    public function deleteFile($file)
    {
        if (file_exists($file))
            unlink($file) or die(false);

        return true;
    }

    public function deleteFolderWithFile($dir)
    {
        if (strlen($dir) === 0 || !is_dir($dir))
            return false;

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir."/".$object) == "dir")
                        $this->deleteFolderWithFile($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }

        return true;
    }

}
