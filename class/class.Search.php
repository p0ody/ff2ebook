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
        // Desktop table
        $return = "<table class=\"table table-hover table-condensed align-left hidden-xs hidden-sm hidden-md\">";
        $return .= "<thead><tr><th>Web Source</th><th>Title - Author</th><th>Updated Date</th><th>Download</th></tr></thead>";
        $return .= "<tbody>";

        /*if ($this->getResults() === null)
            return false;*/
        $i = 1;
        foreach($this->getResults() as $id => $row)
        {
            $id = "collapse-line". $i;
            $epub = "download.php?source=". $row["site"] ."&id=". $row["id"] ."&filetype=epub";
            $mobi = "download.php?source=". $row["site"] ."&id=". $row["id"] ."&filetype=mobi";
            $return .= "<tr>
                            <td class=\"hidden-xs\">". Utils::webSourceReadableURL($row["site"]) ."</td>
                            <td ><a href=\"". Utils::getWebURL($row["id"], $row["site"])  ."\" >". $row["title"] ." - ". $row["author"] ."</a></td>
                            <td class=\"hidden-xs\">". date("Y-m-d", intval($row["updated"])) ."</td>
                            <td class=\"hidden-xs\"><a href=\"". $epub ."\">EPUB</a> <a href=\"". $mobi ."\">MOBI</a></td>
                        </tr>";

            $i++;
        }

        $return .= "</tbody>";
        $return .= "</table>";

        // Mobile table
        $return .= "<table class=\"table table-mobile table-hover table-condensed align-left hidden-lg\">";
        $return .= "<thead><tr><th>Title</th></tr></thead>";
        $return .= "<tbody>";

        /*if ($this->getResults() === null)
            return false;*/
        $i = 1;
        foreach($this->getResults() as $id => $row)
        {
            $id = "collapse-line". $i;
            $epub = "download.php?source=". $row["site"] ."&id=". $row["id"] ."&filetype=epub";
            $mobi = "download.php?source=". $row["site"] ."&id=". $row["id"] ."&filetype=mobi";
            $return .= "<tr role=\"button\" tabindex=\"0\" data-source=\"". Utils::webSourceReadable($row["site"]) ."\"
                            data-source-url=\"". Utils::webSourceURL($row["site"]) ."\"
                            data-fic-url=\"". Utils::getWebURL($row["id"], $row["site"]) ."\"
                            data-updated=\"". date("Y-m-d", intval($row["updated"])) ."\"
                            data-download-epub=\"". $epub ."\"
                            data-download-mobi=\"". $mobi ."\"
                            data-title=\"". $row["title"] ."\"
                            data-author=\"". $row["author"] ."\">

                            <td>". $row["title"] ."</a></td>
                        </tr>";

            $i++;
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
            $query = $pdo->prepare("SELECT * FROM `fic_archive` WHERE `id` LIKE :search OR `title` LIKE :search or `author` LIKE :search ORDER BY `title` LIMIT ". $offset .", ". $limit .";");
            $query->execute(Array("search" => $sqlSearch));
            $this->results = $query->fetchAll();

            $query2 = $pdo->prepare("SELECT COUNT(`id`) as 'count' FROM `fic_archive` WHERE `id` LIKE :search OR `title` LIKE :search or `author` LIKE :search;");
            $query2->execute(Array("search" => $sqlSearch));
            $this->totalCount = intval($query2->fetch()["count"]);

            return true;
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

}