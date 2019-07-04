<?php
require_once("class.dbHandler.php");


class Session
{
    private $dbH;
    /** @var PDO */
    private $pdo;


    function __construct()
    {
        $this->dbH = new dbHandler();
    }


    public function open()
    {
        try
        {
            $this->pdo = $this->dbH->connect();
        }
        catch (PDOException $e)
        {
            return false;
        }

        return true;
    }

    public function close()
    {
        $this->dbH->disconnect();
        return true;
    }

    public function read($id)
    {
        try
        {
            $query = $this->pdo->prepare("SELECT `data` FROM `sessions` WHERE `sessions`.`id` = ?;");
            $query->execute(Array($id));

            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);

                return $result["data"];
            }
            return '';
        }
        catch (PDOException $e)
        {
            return false;

        }
        return true;
    }

    public function write($id, $data)
    {
        try
        {
            $query = $this->pdo->prepare("REPLACE INTO `sessions` (`id`, `access`, `data`) VALUES (?, ?, ?);");
            $query->execute(Array($id, time(), $data));
        }
        catch (PDOException $e)
        {
            return false;
        }

        return true;
    }

    public function destroy($id)
    {
        try
        {
            $query = $this->pdo->prepare("DELETE FROM `sessions` WHERE `id` = ?;");
            $query->execute(Array($id));
        }
        catch (PDOException $e)
        {
            return false;
        }
        return true;
    }

    public function clean($max)
    {
        try
        {
            $old = time() - $max;
            $query = $this->pdo->prepare("DELETE FROM `sessions` WHERE `access` < ?;");
            $query->execute(Array($old));
        }
        catch (PDOException $e)
        {
            return false;
        }
        return true;
    }
}
