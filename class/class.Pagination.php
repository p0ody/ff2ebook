<?php

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


        $return = "<nav><ul class=\"pagination\">";

        $return .= "<li". (($currentPage == 1) ? " class=\"disabled\"": "") ."><a href=\"". $pageURL . 1 ."\" aria-label=\"First\"><<</a></li>"; // First
        $return .= "<li". (($currentPage == 1) ? " class=\"disabled\"": "") ."><a href=\"". $pageURL . ($currentPage - 1) ."\" aria-label=\"Previous\"><</a></li>"; // Previous

        for ($i = 1; $i <= $pageCount; $i++)
        {
            $return .= "<li". (($currentPage == $i) ? " class=\"active\"": "") ."><a href=\"". $pageURL . $i ."\" aria-label=\"Page ". $i ."\">". $i ."</a></li>";
        }

        $return .= "<li". (($currentPage >= $pageCount) ? " class=\"disabled\"": "") ."><a href=\"" . $pageURL . ($currentPage + 1) . "\" aria-label=\"Next\">></a></li>"; // Next
        $return .= "<li". (($currentPage >= $pageCount) ? " class=\"disabled\"": "") ."><a href=\"" . $pageURL . $pageCount . "\" aria-label=\"Last\">>></a></li>"; // Last

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
}