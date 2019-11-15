<?php

class Twig_Extension_Session extends Twig_Extension
{
    public function getName()
    {
        return 'flash';
    }

    public function flash($flash) {
        return array(
            'session' => $flash
        );
    }
}