<?php

use Model\Auth;
use Model\Logout;
use Model\Manager;

require_once '../AltoRouter.php';
require_once '../controller/frontend.php';
require_once '../controller/backend.php';
require_once '../vendor/autoload.php';
require_once '../model/Auth.php';
require_once '../model/Manager.php';
require_once '../model/logout.php';

$router = new AltoRouter();

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader, [
    'debug' => true,
]);
$twig->addExtension(new Twig_Extension_Session());
$twig->addExtension(new \Twig\Extension\DebugExtension());
session_start();

$router->map( 'GET', '/', function() {
    getHomePage();
});

$router->map( 'GET|POST', '/contact', function() {
    getContactPage();
});

$router->map( 'GET|POST', '/projet/[*:slug]-[i:id]', function($slug, $id) {
    getProjectPage($id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/[connection:action]', function($slug, $id) {
    askConnection($slug, $id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/[inscription:action]', function($slug, $id) {
    askInscription($slug, $id);
});


$router->map( 'GET|POST', '/[*:slug]-[i:id]/deconnexion', function($slug, $id){
    askDisconnection($slug, $id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/mot-de-passe-oublie', function($slug, $id) use ($twig){
    $succes_connection = "renseignez votre e-mail pour réinitialiser votre mot de passe";
    print_r ($twig->render('single.twig', ['post' => post($id), 'connectionform' => connectionForm(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'success_connection' => $succes_connection]));
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/reinitialisation-mot-de-passe', function($slug, $id) use ($twig){
    askNewPassword();
    if (!empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) && askNewPassword() == true) {
        $success_connection = 'Vous allez recevoir un email pour réinitialiser votre mot de passe';
        print_r ($twig->render('single.twig', ['post' => post($id), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'success_connection' => $success_connection]));
    } else {
        $error_connection = 'Votre e-mail n\'est pas reconnu';
        print_r ($twig->render('single.twig', ['post' => post($id), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]));
    }
});

//    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'resetPassword') {
//        changePassword();
//        if (filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) && filter_input(INPUT_GET, '', FILTER_SANITIZE_SPECIAL_CHARS) == 'key' && changePassword() == true) {
//            $success_connection = 'Votre nouveau mot de passe est enregistré avec succès, vous pouvez vous connecter';
//            print_r ($twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'accountform' => accountForm(), 'success_connection' => $success_connection]));
//        } else {
//            $error_connection = 'Votre mots de passe n\'est pas enregistré';
//            print_r ($twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]));
//        }


$router->map( 'GET|POST', '/admin', function(){
    getAdminConnection();
});

$router->map( 'GET|POST', '/admin/home', function() use ($twig) {
    if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS))) {
        $error_connection = "Format d'email erroné";
        print_r ($twig->render('homeAdmin.twig', ['adminconnectionform' => adminConnectionForm(), 'error_connection' => $error_connection]));
    } elseif (!empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS)) && askAdminConnection()) {
        $success_connection = 'Vous êtes connecté vous pouvez ajouter des portfolios et modérer les commentaires';
        print_r ($twig->render('homeAdmin.twig', ['success_connection' => $success_connection]));
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        print_r ($twig->render('homeAdmin.twig', ['adminconnectionform' => adminConnectionForm(), 'error_connection' => $error_connection]));
    }
});

$router->map( 'GET|POST', '/admin/projets', function() use ($twig) {
    if (Auth::adminIsLogged()) {
        print_r ($twig->render('postsListAdmin.twig', ['postlist' => listPosts()]));
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        print_r ($twig->render('homeAdmin.twig', ['error_connection' => $error_connection]));
    }
});
$router->map( 'GET|POST', '/admin/ajouter-un-projet', function() use ($twig) {
    if (Auth::adminIsLogged()) {
        $success_add_post = 'Vous pouvez ajouter un projet';
        print_r ($twig->render('addSingle.twig', ['addpostform' => postForm(), 'success_add_post' => $success_add_post]));
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        print_r ($twig->render('homeAdmin.twig', ['error_connection' => $error_connection]));
    }
});

$router->map( 'GET|POST', '/admin/ajout-projet', function() use ($twig) {
    if (!empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
        $realisation_date = filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $technologies = filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS);
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS);
        $intro = filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS);
        if (addPost($title, $content , $realisation_date, $technologies, $url, $intro)) {
            $success_add_post = 'Le projet est ajouté';
            print_r($twig->render('addSingle.twig', ['success_add_post' => $success_add_post]));
        } else {
            throw new Exception('Tous les champs ne sont pas remplis !');
        }
    }
});

$router->map( 'GET|POST', '/admin/editer-projet/[i:id]', function($id) use ($twig) {
    if (Auth::adminIsLogged() && $id > 0) {
        editPostForm($id);
        $success_add_post = 'Vous pouvez ajouter un projet';
        print_r ($twig->render('addSingle.twig', ['editpostform' => editPostForm($id)]));
    } else {
        $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article';
        print_r ($twig->render('homeAdmin.twig', ['error_connection' => $error_connection]));
    }
});

$router->map( 'GET|POST', '/admin/editer-projet/[i:id]', function($id) use ($twig) {
    if (Auth::adminIsLogged() && !empty(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS)) && filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS) > 0 && !empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
        $realisation_date = filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $technologies = filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS);
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS);
        $intro = filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS);
        if (addPost($id, $title, $content , $realisation_date, $technologies, $url, $intro)) {
            $success_add_post = 'Le projet est modifié';
            print_r ($twig->render('addSingle.twig', ['success_add_post' => $success_add_post]));
        }
    } else {
        $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article ou les champs ne sont pas remplis';
        print_r ($twig->render('homeAdmin.twig', ['error_connection' => $error_connection]));
    }
});

$router->map( 'GET|POST', '/admin/commentaires', function() use ($twig) {
        if (Auth::adminIsLogged()) {
            print_r ($twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]));
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            print_r ($twig->render('homeAdmin.twig', ['error_connection' => $error_connection]));
        }
});


$router->map( 'GET|POST', '/admin/commentaires/valider-[i:id]', function($id) use ($twig) {
    if (Auth::adminIsLogged()) {
        validComment($id);
        print_r ($twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]));
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        print_r ($twig->render('homeAdmin.twig', ['error_connection' => $error_connection]));
    }
});

$router->map( 'GET|POST', '/admin/commentaires/supprimer-[i:id]', function($id) use ($twig) {
    if (Auth::adminIsLogged()) {
        deleteComment($id);
        print_r ($twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]));
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        print_r ($twig->render('homeAdmin.twig', ['error_connection' => $error_connection]));
    }
});

$match = $router->match();

if($match !== null){
    call_user_func_array($match['target'],  $match['params']);
}