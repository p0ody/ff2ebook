<?php
require_once __DIR__."/../conf/sql.login.php";

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
        $this->host = SQL_HOST;
        $this->user = SQL_USER;
        $this->passwd = SQL_PASSWD;
        $this->db = SQL_DB;
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
define("SQL_SELECT_FIC", "SELECT * FROM `fic_archive` WHERE `id`=:id AND `site`=:site;");
