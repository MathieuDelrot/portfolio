<?php
use Model\ComManager;
use Model\FormManager;
use Model\Manager;
use Model\MemberManager;
use Model\ProjectManager;
use Model\Logout;
use Model\Auth;
use Model\SessionManager;
use Model\AdminManager;

require_once '../model/Manager.php';
require_once '../model/AdminManager.php';
require_once '../model/MemberManager.php';
require_once '../model/ProjectManager.php';
require_once '../model/FormManager.php';
require_once '../model/ComManager.php';
require_once '../vendor/autoload.php';
require_once 'TwigController.php';
require_once '../model/SessionManager.php';

function getProject($id)
{
    $projectManager = new ProjectManager();
    $project = $projectManager->getProject($id);
    return $project;
}

function getProjects()
{
    $projectManager = new ProjectManager();
    $projects = $projectManager->getProjects();
    return $projects;
}

function getConnectionForm()
{
    $form = new FormManager();
    $connection_form = $form->getConnectionForm();
    return $connection_form;
}

function getComments($id)
{
    $comManager = new ComManager();
    $comments = $comManager->getComments($id);
    return $comments;
}

function getConnection()
{
    $MM = new MemberManager();
    $member = $MM->connection(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
    return $member;
}

function getCreatAccountForm()
{
    $form = new FormManager();
    $account_form = $form->getCreateAccountForm();
    return $account_form;
}

function getCommentForm()
{
    $form = new FormManager();
    $comment_form = $form->getCommentForm();
    return $comment_form;
}

function addAccount($firstName, $email, $password)
{

    $MM = new MemberManager();
    $affectedLines = $MM->createAccount($firstName, $email, $password);

    if ($affectedLines === false) {
        throw new Exception('Impossible de créer votre compte !');
    }
    else {
        $MM->sendEmailSuccess($firstName, $email);
    }

}

function getHomePage()
{

    useTwig('home.twig', ['projectlist' => getProjects()]);
}

function getContactPage()
{
    $form = new FormManager();
    $contact_form = $form->getContactForm();
    useTwig('contact.twig', ['contactform' => $contact_form]);
}

function getSingleTemplate($connection = false, $createAccount = false, $commentForm = false, $resetPasswordForm = false, $id, $error = false, $success = false, $key = false, $newPasswordForm = false)
{
        if($connection == true){
            $connection = getConnectionForm();
        }

        if( $createAccount == true) {
            $createAccount = getCreatAccountForm();
        }

        if($commentForm == true) {
            $commentForm = getCommentForm();
        }

        if($resetPasswordForm == true) {
            $form = new FormManager();
            $resetPasswordForm = $form->getResetPasswordForm();
        }

        if($key == true) {
            return $key;
        }

        if($newPasswordForm == true) {
            $newPasswordForm = newPasswordForm();
        }


        useTwig('single.twig', [
        'project' => getProject($id),
        'commentlist' => getComments($id),
        'error' => $error,
        'success' => $success,
        'connectionform' => $connection,
        'accountform' => $createAccount,
        'commentform' => $commentForm,
        'resetpasswordform' => $resetPasswordForm,
        'key' => $key,
        'newpasswordform' => $newPasswordForm,
        ]);
}


function getProjectPage($id)
{
    if (Auth::isLogged() && $id > 0) {
        getSingleTemplate(false, false, true, false, $id);

    } elseif ($id > 0) {
        getSingleTemplate(true,true,false, false, $id);
    } else
    {
        throw new Exception('Aucun identifiant de portfolio envoyé');
    }

}

function askConnection($slug, $id)
{
    if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
        if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
            $error = "Format d'email erroné";
            getSingleTemplate(true, true,false,false,$id,true, false);

        } else {
            getConnection();
            if (getConnection() or Auth::isLogged()) {
                $success = 'Vous êtes connecté vous pouvez laisser des commentaires';
                getSingleTemplate(false,false,true,false,$id,false, $success);

            } else {
                $error = 'Mauvais identifiant ou mot de passe !';
                getSingleTemplate(true,true,false,false,$id,$error,false);
            }
        }
    } else {
        throw new Exception('Vous n\'avez pas renseigné toutes les données');
    }
}


function askInscription($slug, $id)
{
    if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
        if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
            $error = "Format d'email erroné";
            getSingleTemplate(true,true,false,false,$id,$error,false);
        } elseif (getConnection()) {
            $error = "Vous avez déja un compte, veuillez vous connecter";
            getSingleTemplate(true,true,false,false,$id,$error,false);
        } else {
            addAccount(filter_input(INPUT_POST, 'first_name_account', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
            $success = 'Votre compte est créé vous pouvez laisser des commentaires';
            getSingleTemplate(false,false,true,false,$id,$success,true);
        }
    }
    else {
        throw new Exception('Tous les champs ne sont pas remplis !');
    }
}

function askDisconnection($slug, $id)
{
    if(Auth::isLogged()){
        Auth::disconnect();
        $success= "Vous êtes déconnecté";
        getSingleTemplate(true,true,false,false,$id,false,$success);
    }

}

function addComment($id)
{

    if(Auth::isLogged()){

        if(!empty(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS))){
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);
            $session = new SessionManager();
            $first_name = $session->vars['Auth']['first_name'];
            $commentManager = new ComManager();
            $affectedLines = $commentManager->projectComment($id, $first_name, $comment);

            if ($affectedLines === false) {
                throw new Exception('Impossible d\'ajouter le commentaire !');
            }
            else {
                $success = "Votre commentaire à bien été pris en compte, nous allons le modérer";
                getSingleTemplate(false,false,true,false,$id,false,$success);

            }
        }
    }

}

function askResetingPassword($id)
{
    $success = "renseignez votre e-mail pour réinitialiser votre mot de passe";
    getSingleTemplate(true,true,false,true,$id,$success,true);
}

function askNewPassword($id)
{
    $MM = new MemberManager();
    $findAccount = $MM->forgotPassword(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS), $id, $slug);
    if ($findAccount == true) {
        $success = 'Vous allez recevoir un email pour réinitialiser votre mot de passe';
        getSingleTemplate(true,true,false,false,$id,false,$success);

    } else {
        $error = 'Votre e-mail n\'est pas reconnu';
        getSingleTemplate(true,true,false,true,$id,$error,false);
    }
}

function resetingPassord($id, $key)
{
    $success = "réinitialisez votre mot de passe";
    getSingleTemplate(true,true,false,false,$id,false,$success, $key, true);
}

function newPassword($id, $key)
{
    changePassword($key);
    if (changePassword($key) == true) {
        $success = 'Votre nouveau mot de passe est enregistré avec succès, vous pouvez vous connecter';
        getSingleTemplate(true,true,false,false,$id,false,$success);
    } else {
        $error = 'Votre mots de passe n\'est pas enregistré';
        getSingleTemplate(true,true,false,false,$id,$error,false, $key, true);
    }

}


function checkIfPasswordKeyExist(){
    $MM = new MemberManager();
    $findPassworKey = $MM->findPasswordKey(filter_input(INPUT_GET, 'key', FILTER_SANITIZE_SPECIAL_CHARS));
    return $findPassworKey;
}

function newPasswordForm()
{
    $form = new FormManager();
    $reset_form = $form->getNewPasswordForm();

    return $reset_form;
}

function changePassword($key)
{
    if (filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS)){
        $MM = new MemberManager();
        $resetPassword = $MM->changePassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS),$key);
        return $resetPassword;
    }
}