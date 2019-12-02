<?php

namespace Model;

class Logout
{
    static function disconnection(){
        $_SESSION = array();
        session_destroy();
    }

}