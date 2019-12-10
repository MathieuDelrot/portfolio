<?php

namespace Model;

require_once 'SessionObject.php';

class Auth
{

    static function isLogged()
    {
        $session = new SessionObject();

        if (isset($session->vars['Auth']) && isset($session->vars['Auth']['email']) && isset($session->vars['Auth']['password']) && isset($session->vars['Auth']['first_name'])){
            return true;
        }
        return false;
    }
    static function adminIsLogged()
    {
        $session = new SessionObject();

        if (isset($session->vars['AuthAdmin']) && isset($session->vars['AuthAdmin']['email']) && isset($session->vars['AuthAdmin']['password'])){
            return true;
        }
        return false;
    }
}