<?php
require_once __DIR__."/../conf/config.php";

class dbHandler
{
    private $host;
    private $user;
    private $passwd;
    private $db;
    /** @var PDO */
    private $pdo;
    private $error;

    function __construct()
    {
        $this->host = Config::SQL_HOST;
        $this->user = Config::SQL_USER;
        $this->passwd = Config::SQL_PASSWD;
        $this->db = Config::SQL_DB;
        $this->error = Array();
    }

    public function connect()
    {
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->db;charset=UTF8", $this->user, $this->passwd, $opt);

        return $this->pdo;
    }

    public function disconnect()
    {
        $this->pdo = NULL;

    }

    public function userFriendlyError($code)
    {
        switch($code)
        {
            case 28000:
                return "MySQL: Couldn't connect to database.";

            case 42000:
                return "MySQL: Syntax error.";

            default:
                return "MySQL: Unknown database error.";
        }
    }

    public function handler() { return $this->pdo; }

}


// Predefined statement
define("SQL_SELECT_FIC", "SELECT * FROM `fic_archive` WHERE `id`=:id AND `site`=:site ORDER BY `updated` DESC;");
define("SQL_INSERT_FIC", "INSERT INTO `fic_archive` (`site`, `id`, `title`, `author`, `updated`, `filename`, `lastChecked`) VALUES (:site, :id, :title, :author, :updated, :filename, :lastChecked) ON DUPLICATE KEY UPDATE `updated`=:updated, `lastChecked`=:lastChecked;");
define("SQL_UPDATE_DL_DATE", "UPDATE `fic_archive` SET `lastDL`=". time() ." WHERE `id`=:id");
define("SQL_INSERT_PROXY", "INSERT INTO `proxy_list` (`ip`, `working`, `latency`, `auth`) VALUES (:ip, :working, :latency, :auth) ON DUPLICATE KEY UPDATE `working`=:working, `latency`=:latency, `auth`=:auth;");
define("SQL_SELECT_PROXY_ALL", "SELECT * FROM `proxy_list` ORDER BY `working` DESC, `auth` DESC, `latency` ASC, (`times_down`/`total_hits`) DESC;");
define("SQL_SELECT_PROXY_ALL_AUTH", "SELECT * FROM `proxy_list` WHERE `auth` IS NOT NULL ORDER BY `working` DESC, `latency` ASC, (`times_down`/`total_hits`) DESC;");
define("SQL_SELECT_PROXY_LIMIT", "SELECT * FROM `proxy_list` ORDER BY `working` DESC, `auth` DESC, `latency` ASC, (`times_down`/`total_hits`) DESC LIMIT :limitCount;");
define("SQL_EMPTY_PROXY_LIST", "DELETE * FROM  `proxy_list`;");
define("SQL_CLEAN_PROXY", "DELETE FROM  `proxy_list` WHERE `working`=0 AND `times_down`> 1 AND (`times_down`/`total_hits`)>0.4;"); // Delete proxies that are not working, and have been down at least once and uptime is lower than 40%
define("SQL_PROXY_ADD_BLACKLIST", "DELETE FROM `proxy_list` WHERE `ip`=:ip; REPLACE INTO `proxy_blacklist` (`ip`) VALUES (:ip);");
define("SQL_PROXY_GET_BLACKLIST", "SELECT * FROM `proxy_blacklist`;");
define("SQL_PROXY_IS_BLACKLISTED", "SELECT * FROM `proxy_blacklist` WHERE `ip`=:ip;");
define("SQL_PROXY_UPDATE_UPTIME", "UPDATE `proxy_list` SET `total_hits`=`total_hits`+1, `times_down`=`times_down`+:isWorking WHERE `ip`=:ip;");
define("SQL_UPDATE_LASTCHECKED", "UPDATE `fic_archive` SET `lastChecked`=". time() ." WHERE `id`=:id AND `site`=:site;");