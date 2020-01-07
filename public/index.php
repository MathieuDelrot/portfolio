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

$router->map( 'GET|POST', '/projets', function() {
    getProjectsPage();
});

$router->map( 'GET|POST', '/contact', function() {
    getContactPage();
});

$router->map( 'GET|POST', '/projet/[*:slug]-[i:id]', function($slug, $id) {
    getProjectPage($id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/[connexion:action]', function($slug, $id) {
    askConnection($slug, $id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/[inscription:action]', function($slug, $id) {
    askInscription($slug, $id);
});


$router->map( 'GET|POST', '/[*:slug]-[i:id]/deconnexion', function($slug, $id){
    askDisconnection($slug, $id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/mot-de-passe-oublie', function($slug, $id){
    askResetingPassword($id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/reinitialisation-mot-de-passe', function($slug, $id){
    askNewPassword($id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/reinitialiser-mot-de-passe/[*:key]', function($slug, $id, $key){
    resetingPassord($id, $key);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/nouveau-mot-de-passe-[*:key]', function($slug, $id, $key){
    newPassword($id, $key);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/ajouter-un-commentaire', function($slug, $id){
    addComment($id);
});

$router->map( 'GET|POST', '/admin', function(){
    getAdminConnection();
});

$router->map( 'GET|POST', '/admin/home', function() {
    getAdminHomePage();
});

$router->map( 'GET|POST', '/admin/projets', function() {
    getProjectsAdminPage();
});
$router->map( 'GET|POST', '/admin/ajouter-un-projet', function() {
    addProjectPage();
});

$router->map( 'GET|POST', '/admin/ajout-projet', function() {
    addProject();
});

$router->map( 'GET|POST', '/admin/editer-un-projet/[i:id]', function($id) {
   editProjectPage($id);
});

$router->map( 'GET|POST', '/admin/editer-projet', function(){
    editProject();
});

$router->map( 'GET|POST', '/admin/commentaires', function(){
    getAdminComments();
});


$router->map( 'GET|POST', '/admin/commentaires/valider-[i:id]', function($id){
    validComment($id);
});

$router->map( 'GET|POST', '/admin/commentaires/supprimer-[i:id]', function($id){
    deleteComment($id);
});

$match = $router->match();

if($match !== null){
    call_user_func_array($match['target'],  $match['params']);
}