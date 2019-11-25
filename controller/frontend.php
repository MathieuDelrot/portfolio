<?php

// Chargement des classes

use Model\AccountManager;
use Model\ComManager;
use Model\FormManager;
use Model\Manager;
use Model\PostManager;
use Model\Logout;


require('model/Manager.php');
require_once('model/PostManager.php');
require('model/FormManager.php');
require('model/ComManager.php');
require('model/AccountManager.php');


function postForm()
{
    $form = new FormManager();
    $post_form = $form->getPostForm();

    return $post_form;
}


function addPost($title, $content, $realisation_date, $technologies, $url, $intro)
{

    $postManager = new PostManager();

    $affectedLinesPost = $postManager->post($title, $content, $realisation_date, $technologies, $url, $intro);

    if ($affectedLinesPost === false) {
        throw new Exception('Impossible d\'ajouter le poat !');
    }
    else {

    }

}

function editPostForm()
{
    $dataManager = new PostManager();
    $datas = $dataManager->getPost(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));
    $form = new FormManager();
    $post_form = $form->getEditPostForm($datas['id'],$datas['title'],$datas['content'], $datas['realisation_date'], $datas['technologies'], $datas['url'], $datas['intro']);

    return $post_form;
}

function editPost($id, $title, $content, $realisation_date, $technologies, $url, $intro)
{
    $postManager = new PostManager();

    $affectedLinesPost = $postManager->editPost($id, $title, $content, $realisation_date, $technologies, $url, $intro);

    if ($affectedLinesPost === false) {
        throw new Exception('Impossible d\'ajouter le poat !');
    }
    else {
        return true;
    }
}


function listPosts()
{
    $postManager = new PostManager();
    $posts = $postManager->getPosts();

   return $posts;
}

function post()
{
    $postManager = new PostManager();
    $post = $postManager->getPost(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));

    return $post;
}

function listComment()
{
    $comManager = new ComManager();
    $comments = $comManager->getComments(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));

    return $comments;

}

function listNewComments()
{
    $comManager = new ComManager();
    $comments = $comManager->getNewComments();

    return $comments;
}

function validComment()
{
    $comManager = new ComManager();
    $comments = $comManager->validComment(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));

    return $comments;
}


function deleteComment()
{
    $comManager = new ComManager();
    $comments = $comManager->deleteComment(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));

    return $comments;

}

function commentForm()
{
    $form = new FormManager();
    $comment_form = $form->getCommentForm();

    return $comment_form;
}

function form()
{
    $form = new FormManager();
    $contact_form = $form->getContactForm();

    return $contact_form;
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

function accountForm()
{
    $form = new FormManager();
    $account_form = $form->getCreateAccountForm();
    return $account_form;
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

function connectionForm()
{
    $form = new FormManager();
    $connection_form = $form->getConnectionForm();

    return $connection_form;
}


function askConnection()
{
    $accountManager = new AccountManager();
    $member = $accountManager->connection(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

    return $member;
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

function adminConnectionForm()
{
    $form = new FormManager();
    $admin_connection_form = $form->getAdminConnectionForm();

    return $admin_connection_form;
}


function askAdminConnection()
{
    $accountManager = new AccountManager();
    $admin = $accountManager->connectionAdmin(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

    return $admin;
}
