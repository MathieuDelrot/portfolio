<?php


namespace Model;


class SessionManager
{
    public $vars;

    public function __construct() {
        $this->vars = &$_SESSION;
    }

}