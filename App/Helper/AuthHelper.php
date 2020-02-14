<?php

namespace App\Helper;

class AuthHelper
{
    static function isLogged()
    {
        $session = new SessionHelper();

        if (isset($session->vars['AuthHelper']) && isset($session->vars['AuthHelper']['email']) && isset($session->vars['AuthHelper']['password']) && isset($session->vars['AuthHelper']['firstName'])){
            return true;
        }
        return false;
    }

    static function disconnect()
    {
        $session = new SessionHelper();

        if (isset($session->vars['AuthHelper']) && isset($session->vars['AuthHelper']['email']) && isset($session->vars['AuthHelper']['password']) && isset($session->vars['AuthHelper']['firstName'])){
            session_destroy();
        }

    }

    static function adminIsLogged()
    {
        $session = new SessionHelper();

        if (isset($session->vars['AuthAdmin']) && isset($session->vars['AuthAdmin']['email']) && isset($session->vars['AuthAdmin']['password'])){
            return true;
        }
        return false;
    }
}