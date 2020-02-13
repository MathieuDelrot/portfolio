<?php

namespace App\Controller;


use App\Entity\CommentEntity;
use App\Entity\MemberEntity;
use App\Entity\MessageEntity;
use App\Entity\ProjectEntity;
use App\Model\ComManager;
use App\Model\MessageManager;
use App\Model\FormManager;
use App\Model\Manager;
use App\Model\MemberManager;
use App\Model\ProjectManager;
use App\Model\Auth;
use App\Model\SessionManager;
use App\Model\AdminManager;
use App\Helper\TwigHelper;

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

        $twigController = new TwigHelper();
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

    public function getContactPage($error = null, $success = null)
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

    public function sendMessage()
    {
        if (!empty(filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_SPECIAL_CHARS)) and !empty(filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS) and !empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS)))) {
           $message = new MessageEntity();
           $message->setFirstName(filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_SPECIAL_CHARS));
           $message->setLastName(filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS));
           $message->setEmail(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
           $message->setMessage(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS));
            $affectedLines = $this->messageManager->addMessage($message);

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

    public function getProjectPage($id)
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


    public function askConnection($slug, $id)
    {

        if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
            if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
                $error = "Format d'email erroné";
                $this->twigController->getSingleTemplate(true, true,false,false,$id,$error, false);

            } else {
                $member = new MemberEntity();
                $member->setEmail(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
                $member->setPassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

                $this->memberManager->connection($member);

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


    public function askInscription($slug, $id)
    {
        $member = new MemberEntity();
        $member->setEmail(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
        $member->setPassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
        $connection = $this->memberManager->connection($member);
        if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
            if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
                $error = "Format d'email erroné";
                $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false);

            } elseif ($connection == true) {
                $error = "Vous avez déja un compte, veuillez vous connecter";
                $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false);
            } else {
                $member= new MemberEntity();
                $member->setFirstName(filter_input(INPUT_POST, 'first_name_account', FILTER_SANITIZE_SPECIAL_CHARS));
                $member->setEmail(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
                $member->setPassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
                $inscription = $this->memberManager->createAccount($member);
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

    public function askDisconnection($slug, $id)
    {
        if(Auth::isLogged()){
            Auth::disconnect();
            $success= "Vous êtes déconnecté";
            $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success);
        }
        $this->twigController->getSingleTemplate(true,true,false,false,$id,false,false);

    }

    public function askResetingPassword($id)
    {
        $success = "renseignez votre e-mail pour réinitialiser votre mot de passe";
        $this->twigController->getSingleTemplate(true,true,false,true,$id,$success,false);
    }

    public function askNewPassword($slug, $id)
    {
        $member = new MemberEntity();
        $member->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS));
        $findAccount = $this->memberManager->forgotPassword($member, $id, $slug);
        if ($findAccount == true) {
            $success = 'Vous allez recevoir un email pour réinitialiser votre mot de passe';
            $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success);

        } else {
            $error = 'Votre e-mail n\'est pas reconnu';
            $this->twigController->getSingleTemplate(true,true,false,true,$id,$error,false);
        }
    }

    public function resetingPassword($id, $key)
    {
        $success = "réinitialisez votre mot de passe";
        $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success, $key, true);
    }

    public function newPassword($id, $key)
    {
        if (!empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
            $member = new MemberEntity();
            $member->setPassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
            $member->setPasswordKey($key);
            $resetPassword = $this->memberManager->changePassword($member);
            if ($resetPassword == true) {
                $success = 'Votre nouveau mot de passe est enregistré avec succès, vous pouvez vous connecter';
                $this->twigController->getSingleTemplate(true,true,false,false,$id,false,$success);
            } else {
                $error = 'Votre mots de passe n\'est pas enregistré';
                $this->twigController->getSingleTemplate(true,true,false,false,$id,$error,false, $key, true);
            }
        }

    }



    public function addComment($id)
    {
        if(Auth::isLogged()){
            if(!empty(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS))){
                $comment = new CommentEntity();
                $comment->setContent(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS));
                $comment->setPseudo($this->sessionManager->vars['Auth']['firstName']);
                $affectedLines = $this->commentManager->addComment($comment, $id);
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