<?php


class Utils
{
    public static function webSourceReadable($site)
    {
        switch($site)
        {
            case "ffnet":
                return "<a href=\"http://www.fanfiction.net\" target=\"_blank\">Fanfiction.net</a>";
            case "fpnet":
                return "<a href=\"http://www.fictionpress.com\" target=\"_blank\">Fictionpress.com</a>";
            case "hpff":
                return "<a href=\"http://www.harrypotterfanfiction.com\" target=\"_blank\">HarryPotterFanFiction.com</a>";

            default:
                return $site;

        }
    }

    public static function getWebURL($id, $site)
    {
        switch($site)
        {
            case "ffnet":
                return "http://www.fanfiction.net/s/". $id;
            case "fpnet":
                return "http://www.fictionpress.com/s/". $id;

            default:
                return "#";

        }
    }

    public static function cleanText($text)
    {
        $replace = Array("&nbsp;","<div>", "</div>");
        return str_replace($replace, "", $text);
    }
}