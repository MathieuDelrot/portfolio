<?php

namespace Model;

class Auth
{
    static function isLogged()
    {
        if (isset($_SESSION['Auth']) && isset($_SESSION['Auth']['email']) && isset($_SESSION['Auth']['password']) && isset($_SESSION['Auth']['first_name'])){
            return true;
        }
        return false;
    }
    static function adminIsLogged()
    {
        if (isset($_SESSION['AuthAdmin']) && isset($_SESSION['AuthAdmin']['email']) && isset($_SESSION['AuthAdmin']['password'])){
            return true;
        }
        return false;
    }
}