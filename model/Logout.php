<?php


namespace Model;

class Logout
{
    static function disconnected(){
        $_SESSION = array();
        session_destroy();
    }

}