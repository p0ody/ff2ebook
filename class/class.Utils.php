<?php


class Utils
{
    public static function webSourceReadableURL($site)
    {
        switch($site)
        {
            case "ffnet":
                return "<a href=\"http://www.fanfiction.net\" target=\"_blank\">Fanfiction.net</a>";
            case "fpcom":
                return "<a href=\"http://www.fictionpress.com\" target=\"_blank\">Fictionpress.com</a>";
            case "hpff":
                return "<a href=\"http://www.harrypotterfanfiction.com\" target=\"_blank\">HarryPotterFanFiction.com</a>";

            default:
                return $site;

        }
    }

    public static function webSourceURL($site)
    {
        switch($site)
        {
            case "ffnet":
                return "http://www.fanfiction.net";
            case "fpcom":
                return "http://www.fictionpress.com";
            case "hpff":
                return "http://www.harrypotterfanfiction.com";

            default:
                return $site;

        }
    }

    public static function webSourceReadable($site)
    {
        switch($site)
        {
            case "ffnet":
                return "Fanfiction.net";
            case "fpcom":
                return "Fictionpress.com";
            case "hpff":
                return "HarryPotterFanFiction.com";

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
            case "hpff":
                return "http://www.harrypotterfanfiction.com/viewstory.php?psid=". $id;

            default:
                return "#";

        }
    }

    public static function cleanText($text)
    {
        $replace = Array("&nbsp;","<div>", "</div>");
        $text = html_entity_decode($text);
        return str_replace($replace, "", $text);
    }
}