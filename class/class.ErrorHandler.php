<?php

class ErrorCode
{
    const ERROR_NONE    = -1;
    const ERROR_CRITICAL   = 0;
    const ERROR_WARNING   = 1;
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

        if ($code == ErrorCode::ERROR_CRITICAL)
            $this->sendDie($count - 1);

    }

    public function getAll() { return $this->errorQueue; }

    public function getAllAsJSONReady()
    {
        $json = Array();
        foreach ($this->errorQueue as $key => $entry)
        {
            $json["code_". $key] = $entry->getCode();
            $json["message_". $key] = $entry->getMessage();
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

        $error = Array
        (
            "code_". $queuePos     => $errorEntry->getCode(),
            "message_". $queuePos    => $errorEntry->getMessage()
        );
        die(json_encode(Array("error" => $error)));
    }

    public function hasErrors()
    {
        if (count($this->errorQueue) > 0)
            return true;

        return false;
    }

}