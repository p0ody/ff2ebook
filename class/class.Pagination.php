<?php

define("PAGINATION_MAX_PAGE_SHOWING", 11); //Must be an odd number

class Pagination
{
    private $result, $resultTotal, $resultPerPage;
    function __construct($pageURL/* Page with the GET attributre e.g. ff2ebook.com/archive.php?page=*/, $currentPage, $resultTotal, $resultPerPage)
    {
        $this->resultTotal = $resultTotal;
        $this->resultPerPage = $resultPerPage;

        $pageCount = $this->getPageCount();
        if ($pageCount < 1)
            $pageCount = 1;

        if ($pageCount == 1)
        {
            $this->result = "";
            return;
        }


        $return = "<nav><ul class=\"pagination pagination-sm\">";

        if ($currentPage != 1)
        {
            $return .= "<li><a href=\"" . $pageURL . 1 . "\" aria-label=\"First\"><<</a></li>"; // First
            $return .= "<li><a href=\"" . $pageURL . ($currentPage - 1) . "\" aria-label=\"Previous\"><</a></li>"; // Previous
        }

        $showing = $this->calcShowingPage($currentPage, $pageCount);
        for ($i = 1; $i <= $pageCount; $i++)
        {
            if ($i <  $showing["min"] || $i > $showing["max"])
            {
                if ($i == $showing["min"] - 1 || $i == $showing["max"] + 1)
                    $return .= "<li><a href=\"#\">...</a></li>";

                continue;
            }

            $return .= "<li". (($currentPage == $i) ? " class=\"active\"": "") ."><a href=\"". $pageURL . $i ."\" aria-label=\"Page ". $i ."\">". $i ."</a></li>";
        }

        if ($currentPage < $pageCount)
        {
            $return .= "<li><a href=\"" . $pageURL . ($currentPage + 1) . "\" aria-label=\"Next\">></a></li>"; // Next
            $return .= "<li><a href=\"" . $pageURL . $pageCount . "\" aria-label=\"Last\">>></a></li>"; // Last
        }

        $return .= "</ul></nav>";

        $this->result = $return;
    }

    public function __toString()
    {
        return $this->result;
    }

    public function getPageCount()
    {
        return round($this->resultTotal/$this->resultPerPage);
    }

    private function calcShowingPage($centerNumber, $maxPage)
    {
        $pageEachSide = (PAGINATION_MAX_PAGE_SHOWING - 1)/2;
        if ($maxPage <= PAGINATION_MAX_PAGE_SHOWING)
            return Array("min" => 0, "max" => $maxPage);

        while ($centerNumber - $pageEachSide < 1)
            $centerNumber++;

        while ($centerNumber + $pageEachSide > $maxPage)
            $centerNumber--;

        return Array("min" => $centerNumber - $pageEachSide, "max" => $centerNumber + $pageEachSide);
    }
}