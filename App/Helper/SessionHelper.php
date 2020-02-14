<?php

namespace App\Helper;

class SessionHelper
{

    public $vars;

    public function __construct() {
        $this->vars = &$_SESSION;
    }

}