<?php


namespace Model;


class SessionObject
{
    public $vars;

    public function __construct() {
        $this->vars = &$_SESSION;
    }

}