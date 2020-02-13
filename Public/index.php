<?php

require '../vendor/autoload.php';

use App\Controller\FrontendController;
use App\Controller\BackendController;
use App\Router\AltoRouter;


//ini_set('display_errors', 1);

$router = new AltoRouter();

session_start();

$router->map( 'GET', '/', function() {
    $frontController = new FrontendController();
    $frontController->getHomePage();
});

$router->map( 'GET|POST', '/projets', function() {
    $frontController = new FrontendController();
    $frontController->getProjectsPage();
});

$router->map( 'GET|POST', '/contact', function() {
    $frontController = new FrontendController();
    $frontController->getContactPage();
});

$router->map( 'GET|POST', '/contact/[message:action]', function() {
    $frontController = new FrontendController();
    $frontController->sendMessage();
});


$router->map( 'GET|POST', '/projet/[*:slug]-[i:id]', function($slug, $id) {
    $frontController = new FrontendController();
    $frontController->getProjectPage($id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/[connexion:action]', function($slug, $id) {
    $frontController = new FrontendController();
    $frontController->askConnection($slug, $id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/[inscription:action]', function($slug, $id) {
    $frontController = new FrontendController();
    $frontController->askInscription($slug, $id);
});


$router->map( 'GET|POST', '/[*:slug]-[i:id]/deconnexion', function($slug, $id){
    $frontController = new FrontendController();
    $frontController->askDisconnection($slug, $id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/mot-de-passe-oublie', function($slug, $id){
    $frontController = new FrontendController();
    $frontController->askResetingPassword($id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/reinitialiser-mot-de-passe', function($slug, $id){
    $frontController = new FrontendController();
    $frontController->askNewPassword($slug, $id);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/reinitialiser-mot-de-passe/[*:key]', function($slug, $id, $key){
    $frontController = new FrontendController();
    $frontController->resetingPassword($id,$key);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/nouveau-mot-de-passe-[*:key]', function($slug, $id, $key){
    $frontController = new FrontendController();
    $frontController->newPassword($id, $key);
});

$router->map( 'GET|POST', '/[*:slug]-[i:id]/ajouter-un-commentaire', function($slug, $id){
    $frontController = new FrontendController();
    $frontController->addComment($id);
});

$router->map( 'GET|POST', '/admin', function(){
    $backendController = new BackendController();
    $backendController->getAdminConnection();
});

$router->map( 'GET|POST', '/admin/home', function() {
    $backendController = new BackendController();
    $backendController->getAdminHomePage();
});

$router->map( 'GET|POST', '/admin/projets', function() {
    $backendController = new BackendController();
    $backendController->getProjectsAdminPage();
});
$router->map( 'GET|POST', '/admin/ajouter-un-projet', function() {
    $backendController = new BackendController();
    $backendController->addProjectPage();
});

$router->map( 'GET|POST', '/admin/ajout-projet', function() {
    $backendController = new BackendController();
    $backendController->addProject();
});

$router->map( 'GET|POST', '/admin/editer-un-projet/[i:id]', function($id) {
    $backendController = new BackendController();
    $backendController->editProjectPage($id);
});

$router->map( 'GET|POST', '/admin/editer-projet', function(){
    $backendController = new BackendController();
    $backendController->editProject();
});

$router->map( 'GET|POST', '/admin/commentaires', function(){
    $backendController = new BackendController();
    $backendController->getAdminComments();
});

$router->map( 'GET|POST', '/admin/commentaires/valider-[i:id]', function($id){
    $backendController = new BackendController();
    $backendController->validComment($id);
});

$router->map( 'GET|POST', '/admin/commentaires/supprimer-[i:id]', function($id){
    $backendController = new BackendController();
    $backendController->deleteComment($id);
});


$router->map( 'GET|POST', '/admin/membres', function(){
    $backendController = new BackendController();
    $backendController->getNewMemberList();
});

$router->map( 'GET|POST', '/admin/membres/valider-[i:id]', function($id){
    $backendController = new BackendController();
    $backendController->validMember($id);
});

$router->map( 'GET|POST', '/admin/membres/supprimer-[i:id]', function($id){
    $backendController = new BackendController();
    $backendController->deleteMember($id);
});


$match = $router->match();

if($match !== null){
    call_user_func_array($match['target'],  $match['params']);
}