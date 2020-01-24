<?php

namespace Controller;

use Model\AdminManager;
use Model\ComManager;
use Model\FormManager;
use Model\Manager;
use Model\ProjectManager;
use Model\Logout;
use Model\Auth;
use TwigController;

require_once '../Model/Manager.php';
require_once '../Model/ProjectManager.php';
require_once '../Model/FormManager.php';
require_once '../Model/ComManager.php';
require_once '../vendor/autoload.php';
require_once 'TwigController.php';


class BackendController{

    private $twigController;

    private $formManager;

    private $adminManager;

    private $projectManager;

    private $comManager;


    public function __construct()
    {
        $twigController = new TwigController();
        $this->twigController = $twigController;

        $formManager = new FormManager();
        $this->formManager = $formManager;

        $adminManager = new AdminManager();
        $this->adminManager = $adminManager;

        $projectManager = new ProjectManager();
        $this->projectManager = $projectManager;

        $comManager = new comManager();
        $this->comManager = $comManager;
    }


    public function getAdminConnection()
    {
        $form = $this->formManager->getAdminConnectionForm();

        if (Auth::adminIsLogged()) {
            $success_connection = 'Vous pouvez ajouter des portfolios et modérer les commentaires';
            $this->twigController->useTwig('homeAdmin.twig', [
                'success_connection' => $success_connection
            ]);
        } else {
            $this->twigController->useTwig('homeAdmin.twig', [
                'adminconnectionform' => $form
            ]);
        }
    }

    public function getAdminHomePage()
    {
        $form = $this->formManager->getAdminConnectionForm();

        if (Auth::adminIsLogged()) {
            $success_connection = 'Vous pouvez ajouter des portfolios et modérer les commentaires';
            $this->twigController->useTwig('homeAdmin.twig', [
                'success_connection' => $success_connection
            ]);
        } elseif (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
            $error_connection = "Format d'email erroné";
            $this->twigController->useTwig('homeAdmin.twig', [
                'adminconnectionform' => $form,
                'error_connection' => $error_connection,
            ]);
        } elseif (!empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))) {
            $connection = $this->adminManager->adminConnection(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
            if ($connection == true) {
                $success_connection = 'Vous êtes connecté vous pouvez ajouter des portfolios et modérer les commentaires';
                $this->twigController->useTwig('homeAdmin.twig', ['success_connection' => $success_connection]);
            } else {
                $error_connection = 'Mauvais identifiant ou mot de passe !';
                $this->twigController->useTwig('homeAdmin.twig', [
                    'adminconnectionform' => $form,
                    'error_connection' => $error_connection
                ]);
            }
        }
    }

    public function getProjectsAdminPage()
    {
        if (Auth::adminIsLogged()) {
            $projects = $this->projectManager->getProjects();

            $this->twigController->useTwig('projectsListAdmin.twig', ['projectlist' => $projects]);
        }
        else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }


    public function addProjectPage()
    {
        $form = $this->formManager->getProjectForm();

        if (Auth::adminIsLogged()) {
            $success_add_project = 'Vous pouvez ajouter un projet';
            $this->twigController->useTwig('addSingle.twig', [
                'addprojectform' => $form,
                'success_add_project' => $success_add_project
            ]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', [
                'error_connection' => $error_connection
            ]);
        }
    }


    public function addProject()
    {
        if (!empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $realisation_date = filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $technologies = filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS);
            $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS);
            $intro = filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS);
            $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($title));
            if ($this->projectManager->createProject($title, $slug, $content, $realisation_date, $technologies, $url, $intro)) {
                $success_add_project = 'Le projet est ajouté';
                $this->twigController->useTwig('addSingle.twig', ['success_add_project' => $success_add_project]);
            } else {
                $error_add_project = 'Tous les champs ne sont pas remplis !';
                $this->twigController->useTwig('addSingle.twig', ['error_add_project' => $error_add_project]);
            }
        }
    }


    public function editProjectForm($id)
    {
        $project = $this->projectManager->getProject($id);

        //try catch
        $form = $this->formManager->getEditProjectForm($project['id'],$project['title'],$project['content'], $project['realisation_date'], $project['technologies'], $project['url'], $project['intro']);

        return $form;
    }

    public function editProjectPage($id)
    {
        $editProjectForm = $this->formManager->getEditProjectForm($id);
        if (Auth::adminIsLogged() && $id > 0) {
            $success_add_project = 'Vous pouvez modifier un projet';
            $this->twigController->useTwig('addSingle.twig', [
                'editprojectform' => $editProjectForm,
                'success_add_project' => $success_add_project
            ]);
        } else {
            $error_connection = 'Vous n\'êtes pas autorisé à modifier ce projet';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }

    public function editProject()
    {
        if (Auth::adminIsLogged() && !empty(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS)) && filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS) > 0 && !empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
            $id = filter_input(INPUT_POST, 'id' , FILTER_SANITIZE_SPECIAL_CHARS);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS);
            $realisation_date = filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $technologies = filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS);
            $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS);
            $intro = filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS);
            $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($title));
            if ($this->projectManager->editProject($id, $title, $slug, $content , $realisation_date, $technologies, $url, $intro)) {
                $success_add_project = 'Le projet est modifié';
                $this->twigController->useTwig('addSingle.twig', ['success_add_project' => $success_add_project]);
            }
        } else {
            $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article ou les champs ne sont pas remplis';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }

    }
    public function commentsList()
    {
        $comments = $this->comManager->getNewComments();
        return $comments;
    }

    public function getAdminComments()
    {
        $newComments = $this->comManager->getNewComments();
        if (Auth::adminIsLogged()) {
            $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }

    public function validComment($id)
    {
        $newComments = $this->comManager->getNewComments();
        if (Auth::adminIsLogged()) {
            $this->comManager->validComment($id);
            $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments]);
        }
    }

    public function deleteComment($id)
    {
        $newComments = $this->comManager->getNewComments();
        if (Auth::adminIsLogged()) {
            $this->comManager->deleteComment($id);
            $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }


}