<?php

namespace App\Controller;

use App\Entity\AdminEntity;
use App\Entity\MemberEntity;
use App\Entity\ProjectEntity;
use App\EntityManager\ComManager;
use App\EntityManager\MessageManager;
use App\Helper\FormHelper;
use App\EntityManager\Manager;
use App\EntityManager\MemberManager;
use App\EntityManager\ProjectManager;
use App\Helper\AuthHelper;
use App\Helper\SessionHelper;
use App\EntityManager\AdminManager;
use App\Helper\TwigHelper;


class BackendController{

    private $twigController;

    private $formManager;

    private $adminManager;

    private $projectManager;

    private $comManager;

    private $memberManager;


    public function __construct()
    {
        $twigController = new TwigHelper();
        $this->twigController = $twigController;

        $formManager = new FormHelper();
        $this->formManager = $formManager;

        $adminManager = new AdminManager();
        $this->adminManager = $adminManager;

        $projectManager = new ProjectManager();
        $this->projectManager = $projectManager;

        $comManager = new comManager();
        $this->comManager = $comManager;

        $memberManager = new MemberManager();
        $this->memberManager = $memberManager;

    }


    public function getAdminConnection()
    {
        $form = $this->formManager->getAdminConnectionForm();

        if (AuthHelper::adminIsLogged()) {
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

        if (AuthHelper::adminIsLogged()) {
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
            $admin = new AdminEntity();
            $admin->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS));
            $admin->setPassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
            $connection = $this->adminManager->adminConnection($admin);
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
        if (AuthHelper::adminIsLogged()) {
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

        if (AuthHelper::adminIsLogged()) {
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
            $project = new ProjectEntity();
            $project->setTitle(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setContent(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setRealisationDate( filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setTechnologies(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setUrl(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setIntro(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setSlug(preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($project->getTitle())));
            if ($this->projectManager->createProject($project)) {
                $success_add_project = 'Le projet est ajouté';
                $this->twigController->useTwig('addSingle.twig', ['success_add_project' => $success_add_project]);
            } else {
                $error_add_project = 'Tous les champs ne sont pas remplis !';
                $this->twigController->useTwig('addSingle.twig', ['error_add_project' => $error_add_project]);
            }
        }
    }

    
    public function editProjectPage($id)
    {
        $project = $this->projectManager->getProject($id);
        $form= $this->formManager->getEditProjectForm($project);
        if (AuthHelper::adminIsLogged() && $id > 0) {
            $success_add_project = 'Vous pouvez modifier un projet';
            $this->twigController->useTwig('addSingle.twig', [
                'editprojectform' => $form,
                'success_add_project' => $success_add_project
            ]);
        } else {
            $error_connection = 'Vous n\'êtes pas autorisé à modifier ce projet';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }

    public function editProject()
    {
        if (AuthHelper::adminIsLogged() && !empty(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS)) && filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS) > 0 && !empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
            $project = new ProjectEntity();
            $project->setId(filter_input(INPUT_POST, 'id' , FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setTitle(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setContent(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setRealisationDate( filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setTechnologies(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setUrl(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setIntro(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS));
            $project->setSlug(preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($project->getTitle())));
            if ($this->projectManager->editProject($project)) {
                $success_add_project = 'Le projet est modifié';
                $this->twigController->useTwig('addSingle.twig', ['success_add_project' => $success_add_project]);
            } else {
                $error_add_project = 'Tous les champs ne sont pas remplis !';
                $this->twigController->useTwig('addSingle.twig', ['error_add_project' => $error_add_project]);

            }
        }
        else {
            $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article ou les champs ne sont pas remplis';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }

    }

    public function getAdminComments()
    {
        $newComments = $this->comManager->getNewComments();
        if (AuthHelper::adminIsLogged()) {
            $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }

    public function validComment($id)
    {
        if (AuthHelper::adminIsLogged()) {
            $validation = $this->comManager->validComment($id);
            if($validation == true){
                $newComments = $this->comManager->getNewComments();
                $success = "le commentaire a été validé";
                $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments, 'success' => $success]);
            }else{
                $newComments = $this->comManager->getNewComments();
                $error = "le commentaire n'a pas été validé";
                $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments, 'error' => $error]);
            }

        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }

    public function deleteComment($id)
    {
        if (AuthHelper::adminIsLogged()) {
            $delete = $this->comManager->deleteComment($id);
            if($delete == true){
                $newComments = $this->comManager->getNewComments();
                $success = "le commentaire a été supprimé";
                $this->twigController->useTwig('commentsListAdmin.twig', ['success' => $success, 'commentlist' => $newComments]);
            }else{
                $newComments = $this->comManager->getNewComments();
                $error = "le commentaire n'a pas été supprimé";
                $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments, 'error' => $error]);
            }
            $this->twigController->useTwig('commentsListAdmin.twig', ['commentlist' => $newComments]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }

    public function getNewMemberList()
    {
        $newMember = $this->memberManager->getNewMember();
        
        if (AuthHelper::adminIsLogged()) {
            $this->twigController->useTwig('membersListAdmin.twig', ['memberlist' => $newMember]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }


    public function validMember($id)
    {
        if (AuthHelper::adminIsLogged()) {
            $member = new MemberEntity();
            $member->setId( $id);
            $this->memberManager->validAccount($member);
            $newMember = $this->memberManager->getNewMember();
            $success = 'le compte est validé';
            $this->twigController->useTwig('membersListAdmin.twig', ['memberlist' => $newMember, 'success' => $success]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }

    public function deleteMember($id)
    {
        $newMember = $this->memberManager->getNewMember();
        if (AuthHelper::adminIsLogged()) {
            $member = new MemberEntity();
            $member->setId($id);
            $this->memberManager->deleteAccount($member);
            $success = 'le compte est supprimé';
            $this->twigController->useTwig('membersListAdmin.twig', ['memberlist' => $newMember, 'success' => $success]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            $this->twigController->useTwig('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    }


}