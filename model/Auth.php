<?php

namespace Model;

require_once("model/SessionManager.php");


class Auth
{

    static function isLogged()
    {
        $session = new SessionManager();
        if (isset($session->vars['Auth']) && isset($session->vars['Auth']['email']) && isset($session->vars['Auth']['password']) && isset($session->vars['Auth']['first_name'])){
            return true;
        }else{
            return false;
        }
    }

    static function adminIsLogged()
    {
        $session = new SessionManager();
        if (isset($session->vars['AuthAdmin']) && isset($session->vars['AuthAdmin']['email']) && isset($session->vars['AuthAdmin']['password'])){
            return true;
        }else{
            return false;
        }
    }
}