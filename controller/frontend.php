<?php
use Model\AccountManager;
use Model\ComManager;
use Model\FormManager;
use Model\Manager;
use Model\PostManager;
use Model\Logout;
use Model\Auth;

require_once '../model/Manager.php';
require_once '../model/PostManager.php';
require_once '../model/FormManager.php';
require_once '../model/ComManager.php';
require_once '../model/AccountManager.php';
require_once '../vendor/autoload.php';
require_once 'TwigController.php';

function getPost($id)
{
    $postManager = new PostManager();
    $post = $postManager->getPost($id);
    return $post;
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
    $accountManager = new AccountManager();
    $member = $accountManager->connection(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
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
    $accountManger = new AccountManager();
    $affectedLines = $accountManger->createAccount($firstName, $email, $password);

    if ($affectedLines === false) {
        throw new Exception('Impossible de créer votre compte !');
    }
    else {
        $accountManger->sendEmailSuccess($firstName, $email);
    }

}

function getHomePage()
{
    $postManager = new PostManager();
    $posts = $postManager->getPosts();
    useTwig('home.twig', ['postlist' => $posts]);
}

function getContactPage()
{
    $form = new FormManager();
    $contact_form = $form->getContactForm();
    useTwig('contact.twig', ['contactform' => $contact_form]);
}


function getProjectPage($id)
{
    if (Auth::isLogged() && $id > 0) {
        useTwig('single.twig', [
            'post' => getPost($id),
            'commentform' => getCommentForm(),
            'commentlist' => getComments($id),
        ]);
    } elseif ($id > 0) {
        useTwig('single.twig', [
            'post' => getPost($id),
            'connectionform' => getConnectionForm() ,
            'commentlist' => getComments($id),
            'accountform' => getCreatAccountForm()
        ]);
    } else
    {
        throw new Exception('Aucun identifiant de portfolio envoyé');
    }

}

function askConnection($slug, $id)
{
    if (!empty(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
        if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
            $error_connection = "Format d'email erroné";
            useTwig('single.twig', [
                'post' => getPost($id),
                'connectionform' => getConnectionForm(),
                'commentlist' => getComments($id),
                'accountform' => getCreatAccountForm(),
                'error_connection' => $error_connection
            ]);
        } else {
            getConnection();
            if (getConnection() or Auth::adminIsLogged()) {
                $success_connection = 'Vous êtes connecté vous pouvez laisser des commentaires';
                useTwig('single.twig', [
                    'post' => getPost($id),
                    'commentform' => getCommentForm(),
                    'commentlist' => getComments($id),
                    'success_connection' => $success_connection
                ]);
            } else {
                $error_connection = 'Mauvais identifiant ou mot de passe !';
                useTwig('single.twig', [
                    'post' => getPost($id),
                    'connectionform' => getConnectionForm(),
                    'commentlist' => getComments($id),
                    'member' => getConnection(),
                    'accountform' => getConnectionForm(),
                    'error_connection' => $error_connection

                ]);
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
            $error_inscription = "Format d'email erroné";
            useTwig('single.twig', [
                'post' => getPost($id),
                'connectionform' => getConnectionForm(),
                'commentlist' => getComments($id),
                'accountform' => getConnectionForm(),
                'error_inscription' => $error_inscription
            ]);
        } elseif (getConnection()) {
            $error_inscription = "Vous avez déja un compte, veuillez vous connecter";
            useTwig('single.twig', [
                'post' => getPost($id),
                'connectionform' => getConnectionForm(),
                'commentlist' => getComments($id),
                'accountform' => getConnectionForm(),
                'error_inscription' => $error_inscription
            ]);
        } else {
            addAccount(filter_input(INPUT_POST, 'first_name_account', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
            $success_inscription = 'Votre compte est créé vous pouvez laisser des commentaires';
            useTwig('single.twig', [
                'post' => getPost($id),
                'commentform' => getCommentForm(),
                'commentlist' => getComments($id),
                'success_inscription' => $success_inscription
            ]);
        }
    }
    else {
        throw new Exception('Tous les champs ne sont pas remplis !');
    }
}

function askDisconnection($slug, $id)
{
    Logout::disconnection();
    $succes_connection = "Vous êtes déconnecté";
    useTwig('single.twig', [
        'post' => getPost($id),
        'connectionform' => getConnectionForm(),
        'commentlist' => getComments($id),
        'accountform' => getConnectionForm(),
        'success_connection' => $succes_connection
    ]);
}


function postForm()
{
    $form = new FormManager();
    $post_form = $form->getPostForm();

    return $post_form;
}


function addPost($title, $content, $realisation_date, $technologies, $url, $intro)
{

    $postManager = new PostManager();

//    try {
    $postManager->post($title, $content, $realisation_date, $technologies, $url, $intro);
    return true;
//    } catch(\Exception $e) {
//        throw new Exception('Impossible d\'ajouter le post !');
//    }

}

function editPostForm($id)
{
    $dataManager = new PostManager();
    $datas = $dataManager->getPost($id);
    $form = new FormManager();

    //try catch
    $post_form = $form->getEditPostForm($datas['id'],$datas['title'],$datas['content'], $datas['realisation_date'], $datas['technologies'], $datas['url'], $datas['intro']);

    return $post_form;
}

function editPost($id, $title, $content, $realisation_date, $technologies, $url, $intro)
{
    $postManager = new PostManager();

    $affectedLinesPost = $postManager->editPost($id, $title, $content, $realisation_date, $technologies, $url, $intro);

    //try catch
    if ($affectedLinesPost === false) {
        throw new Exception('Impossible d\'ajouter le poat !');
    }
    else {
        return true;
    }
}


function listNewComments()
{
    $comManager = new ComManager();
    $comments = $comManager->getNewComments();

    return $comments;
}

function validComment($id)
{
    $comManager = new ComManager();
    $comments = $comManager->validComment($id);
    return $comments;
}


function deleteComment($id)
{
    $comManager = new ComManager();
    $comments = $comManager->deleteComment($id);

    return $comments;

}


function addComment($postId, $first_name, $comment)
{
    $commentManager = new ComManager();
    $affectedLines = $commentManager->postComment($postId, $first_name, $comment);

    if ($affectedLines === false) {
        throw new Exception('Impossible d\'ajouter le commentaire !');
    }
    else {
        return true;
    }

}




function askResetingPassword()
{
    $form = new FormManager();
    $ask_reseting_form = $form->getResetPasswordForm();

    return $ask_reseting_form;
}

function askNewPassword()
{
    $accountManager = new AccountManager();
    $findAccount = $accountManager->forgotPassword(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS));

    return $findAccount;
}

function checkIfPasswordKeyExist(){
    $accountManager = new AccountManager();
    $findPassworKey = $accountManager->findPasswordKey(filter_input(INPUT_GET, 'key', FILTER_SANITIZE_SPECIAL_CHARS));
    return $findPassworKey;
}

function newPasswordForm()
{
    $form = new FormManager();
    $reset_form = $form->getNewPasswordForm();

    return $reset_form;
}

function changePassword()
{
    $accountManager = new AccountManager();
    $resetPassword = $accountManager->changePassword(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_GET, 'key', FILTER_SANITIZE_SPECIAL_CHARS));
    return $resetPassword;
}


function askAdminConnection()
{
    $accountManager = new AccountManager();
    $admin = $accountManager->connectionAdmin(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

    return $admin;
}
