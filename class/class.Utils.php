<?php


class Utils
{
    public static function webSourceReadableURL($site)
    {
        switch($site)
        {
            case "ffnet":
                return "<a href=\"http://www.fanfiction.net\" target=\"_blank\">". Utils::webSourceReadable($site) ."</a>";
            case "fpcom":
                return "<a href=\"http://www.fictionpress.com\" target=\"_blank\">". Utils::webSourceReadable($site) ."</a>";
            case "hpff":
                return "<a href=\"http://www.harrypotterfanfiction.com\" target=\"_blank\">". Utils::webSourceReadable($site) ."</a>";
            case "hpffa":
                return "<a href=\"http://www.hpfanficarchive.com\" target=\"_blank\">". Utils::webSourceReadable($site) ."</a>";
            case "fhcom":
                return "<a href=\"http://www.fictionhunt.com\" target=\"_blank\">". Utils::webSourceReadable($site) ."</a>";
            case "wattpad":
                return "<a href=\"https://www.wattpad.com\" target=\"_blank\">". Utils::webSourceReadable($site) ."</a>";

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
            case "hpffa":
                return "http://www.hpfanficarchive.com";
            case "fhcom":
                return "http://www.fictionhunt.com";
            case "wattpad":
                return "https://www.wattpad.com";

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
            case "hpffa":
                return "HPFanFicArchive.com";
            case "fhcom":
                return "FictionHunt.com";
            case "wattpad":
                return "Wattpad.com";


            default:
                return $site;

        }
    }

    public static function getWebURL($id, $site)
    {
        switch($site)
        {
            case "ffnet":
            case "ff":
                return "http://www.fanfiction.net/s/". $id;
            case "fpcom":
                return "http://www.fictionpress.com/s/". $id;
            case "hpff":
                return "http://www.harrypotterfanfiction.com/viewstory.php?psid=". $id;
            case "hpffa":
                return "http://www.hpfanficarchive.com/stories/viewstory.php?sid=". $id;
            case "fhcom":
                return "http://fictionhunt.com/read/". $id;
            case "wattpad":
                return "https://wattpad.com/story/". $id;

            default:
                return "#";

        }
    }

    public static function cleanText($text)
    {
        $text = strip_tags($text, "<p><b><br><u><i>");
        $text = Utils::removeClassAndIDs($text);
        $replace = Array("&nbsp;", "<p>&nbsp;</p>");
        $text = str_replace($replace, "", $text);
        return html_entity_decode($text);;
    }

    public static function removeAndSymbol($text)
    {
        return str_replace("&", "and", $text);
    }

    public static function removeClassAndIDs($text)
    {
        $new = preg_replace("#(<.+?) (?:class|id)?=(?:'|\"+?).*?(?:'|\"+?)(.*?>)#si", "$1$2", $text);

        if ($new === null)
            return $text;

        return $new;
    }

    // Added this because when using Selenium, for some reason, some single quote are replace by double quote
    public static function regexOnSource($regex, $source, &$matches)
    {
        $regex = preg_replace("/('|\")/", "(?:'|\")", $regex);

        return preg_match($regex, $source, $matches);
    }

    public static function regexAllOnSource($regex, $source, &$matches)
    {
        $regex = preg_replace("/('|\")/", "(?:'|\")", $regex);

        return preg_match_all($regex, $source, $matches);
    }

	public static function removeEmoji(string $string): string
	{
		$symbols = "\x{1F100}-\x{1F1FF}" // Enclosed Alphanumeric Supplement
		. "\x{1F300}-\x{1F5FF}" // Miscellaneous Symbols and Pictographs
		. "\x{1F600}-\x{1F64F}" //Emoticons
		. "\x{1F680}-\x{1F6FF}" // Transport And Map Symbols
		. "\x{1F900}-\x{1F9FF}" // Supplemental Symbols and Pictographs
		. "\x{2600}-\x{26FF}" // Miscellaneous Symbols
		. "\x{2700}-\x{27BF}"; // Dingbats

		return preg_replace('/[' . $symbols . ']+/u', '', $string);
	}
}
