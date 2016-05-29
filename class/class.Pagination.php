<?php

class Pagination
{
    private $result;
    function __construct($pageURL/* Page with the GET attributre e.g. ff2ebook.com/archive.php?page=*/, $currentPage, $resultTotal, $resultPerPage)
    {
        $pageCount = round($resultTotal/$resultPerPage);
        if ($pageCount < 1)
            $pageCount = 1;


        $return = "<nav><ul class=\"pagination\">";
        if ($currentPage > 1)
        {
            $return .= "<li><a href=\"". $pageURL . 1 ."\" aria-label=\"First\"><<</a></li>";
            $return .= "<li><a href=\"". $pageURL . ($currentPage - 1) ."\" aria-label=\"Previous\"><</a></li>";
        }

        for ($i = 1; $i <= $pageCount; $i++)
        {
            $return .= "<li><a href=\"". $pageURL . $i ."\" aria-label=\"Page ". $i ."\">". $i ."</a></li>";
        }

        if ($currentPage < $pageCount)
        {
            $return .= "<li><a href=\"" . $pageURL . ($currentPage + 1) . "\" aria-label=\"Next\">></a></li>";
            $return .= "<li><a href=\"" . $pageURL . $pageCount . "\" aria-label=\"Last\">>></a></li>";
        }
        $return .= "</ul></nav>";

        $this->result = $return;
    }

    public function __toString()
    {
        return $this->result;
    }
}