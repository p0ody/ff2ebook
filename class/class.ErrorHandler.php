<?php

class ErrorCode
{
    const ERROR_NONE        = -1;
    const ERROR_CRITICAL    = 0;
    const ERROR_WARNING     = 1;
    const ERROR_BLACKLISTED = 2;
}

class ErrorEntry
{
    private $code, $message;

    public function __construct($code, $message = "")
    {
        $this->code = $code;
        $this->message = $message;
    }


    public function getCode() { return $this->code; }
    public function getMessage() { return $this->message; }
}

class ErrorHandler
{
    private $errorQueue = Array();


    public function addNew($code, $message)
    {
        $count = array_push($this->errorQueue, new ErrorEntry($code, $message));

        if ($code == ErrorCode::ERROR_CRITICAL || $code == ErrorCode::ERROR_BLACKLISTED)
            $this->sendDie($count - 1);

    }

    public function getAll() { return $this->errorQueue; }

    public function getAllAsJSONReady()
    {
        $json = Array();
        foreach ($this->errorQueue as $key => $entry)
        {
            array_push($json, ["code" => $entry->getCode(), "message" => $entry->getMessage()]);
        }

        return $json;
    }


    public function clear()
    {
        $this->errorQueue = Array();
    }

    public function sendDie($queuePos)
    {

        $errorEntry = $this->errorQueue[$queuePos];
        if (!isset($errorEntry))
            $errorEntry = new ErrorEntry(ErrorCode::ERROR_CRITICAL);
        $json = [];
        array_push($json, ["code" => $errorEntry->getCode(), "message" => $errorEntry->getMessage()]);
        die(json_encode(["error" => $json]));
    }

    public function hasErrors()
    {
        if (count($this->errorQueue) > 0)
            return true;

        return false;
    }

}
