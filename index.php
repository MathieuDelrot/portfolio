<?php
require('controller/frontend.php');
require ('vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader ('view/frontend');
$twig = new \Twig\Environment($loader);

try {
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'listPosts') {

            echo $twig->render('home.twig', ['postlist' => listPosts()]);
        }
        elseif ($_GET['action'] == 'post') {
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                echo $twig->render('single.twig', ['post' => post()]);
            }
            else {
                throw new Exception('Aucun identifiant de billet envoyé');
            }
        }
        elseif ($_GET['action'] == 'contact'){
            echo $twig->render('contact.twig', ['form' => form()]);
        }
        }
    else {
        echo $twig->render('home.twig', ['postlist' => listPosts()]);
    }
}
catch(Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
