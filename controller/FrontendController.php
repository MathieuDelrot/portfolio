<?php

namespace Controller;

require_once '../vendor/autoload.php';
require_once '../Model/ProjectManager.php';
require_once '../Model/FormManager.php';
require_once '../Model/Auth.php';
require_once '../Model/ComManager.php';
require_once '../Model/MessageManager.php';
require_once '../Model/Manager.php';
require_once '../Model/MemberManager.php';
require_once '../Model/SessionManager.php';
require_once 'TwigController.php';


use Model\ComManager;
use Model\MessageManager;
use Model\FormManager;
use Model\Manager;
use Model\MemberManager;
use Model\ProjectManager;
use Model\Auth;
use Model\SessionManager;
use Model\AdminManager;
use TwigController;

class FrontendController{


    private $projectManager;

    private $commentManager;

    private $formManager;

    private $memberManager;

    private $messageManager;

    private $sessionManager;

    private $twigController;


    public function __construct()
    {
        $projectManager = new ProjectManager();
        $this->projectManager = $projectManager;

        $commentManager = new ComManager();
        $this->commentManager = $commentManager;

        $formManager = new FormManager();
        $this->formManager = $formManager;

        $memberManager = new MemberManager();
        $this->memberManager = $memberManager;

        $messageManager = new MessageManager();
        $this->messageManager = $messageManager;

        $sessionManager = new SessionManager();
        $this->sessionManager = $sessionManager;

        $twigController = new TwigController();
        $this->twigController = $twigController;
    }


    public function getHomePage()
    {
        $projects = $this->projectManager->getLastProjects();
        $this->twigController->useTwig('home.twig',['projectlist' => $projects]);
    }


    public function getProjectsPage()
    {
        $projects = $this->projectManager->getProjects();
        $this->twigController->useTwig('projects.twig', ['projectlist' => $projects]);
    }

    function getContactPage($error = null, $success = null)
    {
        $form = $this->formManager->getContactForm();
        if(isset($error)){
            $this->twigController->useTwig('contact.twig', [
                'error' => $error,
                'contactform' => $form
            ]);
        }elseif (isset($success)){
            $this->twigController->useTwig('contact.twig', [
                'success' => $success,
                'contactform' => $form
            ]);
        }else{
            $this->twigController->useTwig('contact.twig', ['contactform' => $form]);
        }
    }

    function sendMessage()
    {
        if (!empty(filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_SPECIAL_CHARS)) and !empty(filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS) and !empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS)))) {
            $firstName = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_SPECIAL_CHARS);
            $lastName = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
            $affectedLines = $this->messageManager->addMessage($firstName, $lastName, $email, $message);

            if ($affectedLines === false) {
                $error = "Votre message n'a pas été envoyé";
                $success = null;
            } else {
                $error = null;
                $success = "Votre message à bien été envoyé";
            }
        } else {
            $error = "Votre message n'a pas été envoyé";
            $success = null;
        }
        $this->getContactPage($error, $success);

    }

    function getProjectPage($id)
    {
        if (Auth::isLogged() && $id > 0) {
            $this->twigController->getSingleTemplate(false, false, true, false, $id);

        } elseif ($id > 0) {
            $this->twigController->getSingleTemplate(true,true,false, false, $id);
        } else {
            $error = 'Aucun identifiant de portfolio envoyé';
            $this->twigController->getSingleTemplate(true,true,false, false, $id, $error);
        }
    }


    function askConnection($slug, $id)
    {

        if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
            if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
                $error = "Format d'email erroné";
                $this->twigController->getSingleTemplate(true, true,false,false,$id,$error, false);

            } else {
                $member = $this->memberManager->connection(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

                if ($member == true or Auth::isLogged()) {
                    $success = 'Vous êtes connecté vous pouvez laisser des commentaires';
                    $this->twigController->getSingleTemplate(false,false,true,false,$id,false, $success);

                } else {
                    $error = 'Mauvais identifiant ou mot de passe !';
                    $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false);
                }
            }
        } else {
            $error = 'Toutes les donnés ne sont pas renseignées';
            $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false);
        }
    }


    function askInscription($slug, $id)
    {
        $member = $this->memberManager->connection(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
        if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
            if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
                $error = "Format d'email erroné";
                $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false);

            } elseif ($member == true) {
                $error = "Vous avez déja un compte, veuillez vous connecter";
                $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false);
            } else {
                $inscription = $this->memberManager->createAccount(filter_input(INPUT_POST, 'first_name_account', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
                if($inscription == true){
                    $success = 'Votre compte est en cours de validation vous recevrez un email quand votre compte sera validé, vous pourrez alors laisser des commentaires';
                    $this->twigController->getSingleTemplate(true,true,false,false,$id, false,$success);
                }else{
                    $error = 'Votre compte n\'a pas été créé';
                    $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false);
                }
            }
        }
        else {
            $error = 'Tous les champs n\'ont pas été remplis';
            $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false);
        }
    }

    function askDisconnection($slug, $id)
    {
        if(Auth::isLogged()){
            Auth::disconnect();
            $success= "Vous êtes déconnecté";
            $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success);
        }
        $this->twigController->getSingleTemplate(true,true,false,false,$id,false,false);

    }

    function askResetingPassword($id)
    {
        $success = "renseignez votre e-mail pour réinitialiser votre mot de passe";
        $this->twigController->getSingleTemplate(true,true,false,true,$id,$success,false);
    }

    function askNewPassword($slug, $id)
    {
        $findAccount = $this->memberManager->forgotPassword(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS), $id, $slug);
        if ($findAccount == true) {
            $success = 'Vous allez recevoir un email pour réinitialiser votre mot de passe';
            $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success);

        } else {
            $error = 'Votre e-mail n\'est pas reconnu';
            $this->twigController->getSingleTemplate(true,true,false,true,$id,$error,false);
        }
    }

    function resetingPassword($id, $key)
    {
        $success = "réinitialisez votre mot de passe";
        $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success, $key, true);
    }

    function newPassword($id, $key)
    {
        $resetPassword = false;
        if (!empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
            $resetPassword = $this->memberManager->changePassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS), $key);
//        return $resetPassword;
            $success = 'Votre nouveau mot de passe est enregistré avec succès, vous pouvez vous connecter';
            $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success);
        }
        if ($resetPassword == true) {
            $success = 'Votre nouveau mot de passe est enregistré avec succès, vous pouvez vous connecter';
            $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success);
        } else {
            $error = 'Votre mots de passe n\'est pas enregistré';
            $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false, $key, true);
        }

    }



    function addComment($id)
    {
        if(Auth::isLogged()){
            if(!empty(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS))){
                $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);
                $first_name = $this->sessionManager->vars['Auth']['first_name'];
                $affectedLines = $this->commentManager->addComment($id, $first_name, $comment);
                if ($affectedLines === false) {
                    $error = "Impossible d'ajouter votre commentaire";
                    $this->twigController->getSingleTemplate(false,false,true,false,$id,$error);

                }
                else {
                    $success = "Votre commentaire à bien été pris en compte, nous allons le modérer";
                    $this->twigController->getSingleTemplate(false,false,true,false,$id,false,$success);

                }
            }
        }

    }


}