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
}