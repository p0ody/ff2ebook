<?php
require_once("conf/config.php");

// if in poprtable mode - use default php sessions
if (! PORTABLE_MODE) {
    require_once("../class/class.sessionHandler.php");

    $session = new Session();

    session_set_save_handler(
        Array($session, "open"),
        Array($session, "close"),
        Array($session, "read"),
        Array($session, "write"),
        Array($session, "destroy"),
        Array($session, "clean")
    );
}