<?php

function useTwig($template, array $variables){
    $loader = new \Twig\Loader\FilesystemLoader('../view');
    $twig = new \Twig\Environment($loader, [
    'debug' => true,
    ]);
    $twig->addExtension(new Twig_Extension_Session());
    $twig->addExtension(new \Twig\Extension\DebugExtension());
    print_r($twig->render($template, $variables));
}