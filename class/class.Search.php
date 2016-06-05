<?php
require_once __DIR__."/class.dbHandler.php";
require_once __DIR__."/class.Utils.php";


class Search
{
    private $results;
    private $totalCount;

    function __construct($searchFor, $currentPage, $limit)
    {
        $this->search($searchFor, $currentPage, $limit);
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getResultsCount()
    {
        return count($this->results);
    }

    public function getTotalCount() { return $this->totalCount; }

    public function getFormaattedResults()
    {
        $return = "<table class=\"table table-hover table-responsive\">";
        $return .= "<thead><tr><th>Web Source</th><th>Title - Author</th><th>Updated Date</th><th>Download</th></tr></thead>";
        $return .= "<tbody>";

        /*if ($this->getResults() === null)
            return false;*/

        foreach($this->getResults() as $id => $row)
        {
            $epub = "download.php?source=". $row["site"] ."&id=". $row["id"] ."&filetype=epub";
            $mobi = "download.php?source=". $row["site"] ."&id=". $row["id"] ."&filetype=mobi";
            $return .= "<tr><td>". Utils::webSourceReadable($row["site"]) ."</td><td><a href=\"". Utils::getWebURL($row["id"], $row["site"])  ."\">". $row["title"] ." - ". $row["author"] ."</a></td><td>". date("Y-m-d", intval($row["updated"])) ."</td><td><a href=\"". $epub ."\">EPUB</a> <a href=\"". $mobi ."\">MOBI</a></td></tr>";
        }

        $return .= "</tbody>";
        $return .= "</table>";

        return $return;
    }

    private function search($searchFor, $currentPage, $limit)
    {
        try
        {
            $db = new dbHandler();
            $pdo = $db->connect();

            $sqlSearch = "%". str_replace(" ", "%", $searchFor) ."%";

            $offset = ($currentPage == 1 ? "0" : ($currentPage*$limit) - $limit);
            $query = $pdo->prepare("SELECT * FROM `fic_archive` WHERE `id` LIKE :search OR `title` LIKE :search or `author` LIKE :search LIMIT ". $offset .", ". $limit .";");
            $query->execute(Array("search" => $sqlSearch));
            $this->results = $query->fetchAll();

            $query2 = $pdo->prepare("SELECT COUNT(`id`) as 'count' FROM `fic_archive` WHERE `id` LIKE :search OR `title` LIKE :search or `author` LIKE :search;");
            $query2->execute(Array("search" => $sqlSearch));
            $this->totalCount = intval($query2->fetch()["count"]);

            return true;
        }
        catch(PDOException $e)
        {
            //die($e->getMessage());
            return false;
        }
    }

}