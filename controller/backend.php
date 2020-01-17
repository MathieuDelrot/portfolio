<?php


use Model\AdminManager;
use Model\ComManager;
use Model\FormManager;
use Model\Manager;
use Model\ProjectManager;
use Model\Logout;
use Model\Auth;

require_once '../model/Manager.php';
require_once '../model/ProjectManager.php';
require_once '../model/FormManager.php';
require_once '../model/ComManager.php';
require_once '../vendor/autoload.php';
require_once 'TwigController.php';


function getAdminConnectionForm()
{
    $form = new FormManager();
    $admin_connection_form = $form->getAdminConnectionForm();

    return $admin_connection_form;
}

function getAdminConnection()
{
    if (Auth::adminIsLogged()) {
        $success_connection = 'Vous pouvez ajouter des portfolios et modérer les commentaires';
        useTwig('homeAdmin.twig', [
            'success_connection' => $success_connection
        ]);
    } else {
        useTwig('homeAdmin.twig', [
            'adminconnectionform' => getAdminConnectionForm()
        ]);
    }
}

function getAdminHomePage()
{
    if (Auth::adminIsLogged()) {
        $success_connection = 'Vous pouvez ajouter des portfolios et modérer les commentaires';
        useTwig('homeAdmin.twig', [
            'success_connection' => $success_connection
        ]);
    } elseif (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
        $error_connection = "Format d'email erroné";
        useTwig('homeAdmin.twig', [
            'adminconnectionform' => getAdminConnectionForm(),
            'error_connection' => $error_connection,
        ]);
    } elseif (!empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))) {
        $AM = new AdminManager();
        $connection = $AM->adminConnection(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
        if ($connection == true) {
            $success_connection = 'Vous êtes connecté vous pouvez ajouter des portfolios et modérer les commentaires';
            useTwig('homeAdmin.twig', ['success_connection' => $success_connection]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            useTwig('homeAdmin.twig', [
                'adminconnectionform' => getAdminConnectionForm(),
                'error_connection' => $error_connection
            ]);
        }
    }
}

function getProjectsAdminPage()
{
    if (Auth::adminIsLogged()) {
        useTwig('projectsListAdmin.twig', ['projectlist' => getProjects()]);
    }
    else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
    }
}

function projectForm()
{
    $form = new FormManager();
    $project_form = $form->getProjectForm();

    return $project_form;
}


function addProjectPage()
{
    if (Auth::adminIsLogged()) {
        $success_add_project = 'Vous pouvez ajouter un projet';
        useTwig('addSingle.twig', [
            'addprojectform' => projectForm(),
            'success_add_project' => $success_add_project
        ]);
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        useTwig('homeAdmin.twig', [
            'error_connection' => $error_connection
        ]);
    }
}


function addProject()
{
    if (!empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
        $realisation_date = filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $technologies = filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS);
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS);
        $intro = filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS);
        $projectManager = new ProjectManager();
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($title));
        if ($projectManager->createProject($title, $slug, $content, $realisation_date, $technologies, $url, $intro)) {
            $success_add_project = 'Le projet est ajouté';
            useTwig('addSingle.twig', ['success_add_project' => $success_add_project]);
        } else {
            $error_add_project = 'Tous les champs ne sont pas remplis !';
            useTwig('addSingle.twig', ['error_add_project' => $error_add_project]);
        }
    }
}


function editProjectForm($id)
{
    $dataManager = new ProjectManager();
    $datas = $dataManager->getProject($id);
    $form = new FormManager();

    //try catch
    $project_form = $form->getEditProjectForm($datas['id'],$datas['title'],$datas['content'], $datas['realisation_date'], $datas['technologies'], $datas['url'], $datas['intro']);

    return $project_form;
}

function editProjectPage($id)
{
    if (Auth::adminIsLogged() && $id > 0) {
        $success_add_project = 'Vous pouvez modifier un projet';
        useTwig('addSingle.twig', [
            'editprojectform' => editProjectForm($id),
            'success_add_project' => $success_add_project
        ]);
    } else {
        $error_connection = 'Vous n\'êtes pas autorisé à modifier ce projet';
        useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
    }
}

function editProject()
{
    if (Auth::adminIsLogged() && !empty(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS)) && filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS) > 0 && !empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
        $id = filter_input(INPUT_POST, 'id' , FILTER_SANITIZE_SPECIAL_CHARS);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
        $realisation_date = filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $technologies = filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS);
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS);
        $intro = filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS);
        $project = new ProjectManager();
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($title));
        if ($project->editProject($id, $title, $slug, $content , $realisation_date, $technologies, $url, $intro)) {
            $success_add_project = 'Le projet est modifié';
            useTwig('addSingle.twig', ['success_add_project' => $success_add_project]);
        }
    } else {
        $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article ou les champs ne sont pas remplis';
        useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
    }

}
function commentsList()
{
    $comManager = new ComManager();
    $comments = $comManager->getNewComments();
    return $comments;
}

function getAdminComments()
{
    if (Auth::adminIsLogged()) {
        useTwig('commentsListAdmin.twig', ['commentlist' => commentsList()]);
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
    }
}

function validComment($id)
{

    if (Auth::adminIsLogged()) {
        $comManager = new ComManager();
        $comManager->validComment($id);
        useTwig('commentsListAdmin.twig', ['commentlist' => commentsList()]);
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        useTwig('commentsListAdmin.twig', ['commentlist' => commentsList()]);
    }
}

function deleteComment($id)
{
    if (Auth::adminIsLogged()) {
        $comManager = new ComManager();
        $comManager->deleteComment($id);
        print_r($twig->render('commentsListAdmin.twig', ['commentlist' => commentsList()]));
    } else {
        $error_connection = 'Mauvais identifiant ou mot de passe !';
        print_r($twig->render('homeAdmin.twig', ['error_connection' => $error_connection]));
    }
}
