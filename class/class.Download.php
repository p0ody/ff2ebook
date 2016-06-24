<?php
require_once(__DIR__."/../conf/config.php");
require_once("class.base.handler.php");
require_once("class.dbHandler.php");
require_once("class.DownloadFile.php");

if (PORTABLE_MODE)
{
    require_once(__DIR__."/../sqlSession.php");
    session_start();
}

class Download
{
    private $id, $source, $title, $author, $updated;

    function __construct($source, $id)
        /** @var BaseHandler $fic */
    {
        $this->id = $id;
        $this->source = $source;
    }

    public function asEpub()
    {
        try
        {
            if (PORTABLE_MODE)
            {
                $this->title = $_SESSION[$this->source . "_" . $this->id . "_title"];
                $this->author = $_SESSION[$this->source . "_" . $this->id . "_author"];
                $this->updated = $_SESSION[$this->source . "_" . $this->id . "_updated"];
            }
            else
            {
                $db = new dbHandler();
                $pdo = $db->connect();

                $query = $pdo->prepare(SQL_SELECT_FIC);
                $query->execute(Array("id" => $this->id, "site" => $this->source));

                if ($query->rowCount() === 1)
                {
                    $result = $query->fetch();

                    $this->title = $result["title"];
                    $this->author = $result["author"];
                    $this->updated = $result["updated"];
                }
                else
                    throw new PDOException("Could not find fic in database.");
            }

                $filename = $this->source ."_". $this->id ."_". $this->updated;

                if (!file_exists("archive/$filename.epub"))
                    return false;

                return new DownloadFile($filename .".epub", $this->title, $this->author);
        }
        catch(PDOException $e)
        {
            return false;
            //die($e->getMessage());

        }
    }

    public function asMobi()
    {
        try
        {
            $db = new dbHandler();
            $pdo = $db->connect();

            $query = $pdo->prepare(SQL_SELECT_FIC);
            $query->execute(Array("id" => $this->id, "site" => $this->source));

            if ($query->rowCount() === 1)
            {
                $result = $query->fetch();

                $this->title = $result["title"];
                $this->author = $result["author"];
                $this->updated = $result["updated"];
            }
            else
                throw new PDOException("Could not find fic in database.");

            $filename = $this->source ."_". $this->id ."_". $this->updated;

            if (@file_exists("archive/$filename.mobi"))
                return new DownloadFile($filename .".mobi", $this->title, $this->author);


            if (!@file_exists("archive/$filename.epub"))
                return false;

            exec("\"./converter/kindlegen\" \"./archive/$filename.epub\" -locale en 2>&1", $returnArray, $return);

            if ($return !== 1)
                return false;

            if (!@file_exists("archive/$filename.mobi"))
                return false;


            return new DownloadFile($filename .".mobi", $this->title, $this->author);
        }
        catch(PDOException $e)
        {
            return false;
            //die($e->getMessage());

        }
    }

    public function asPDF()
    {

    }

}