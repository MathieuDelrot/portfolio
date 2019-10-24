<?php

// Chargement des classes

use Model\AccountManager;
use Model\ComManager;
use Model\FormManager;
use Model\Manager;
use Model\PostManager;


require('model/Manager.php');
require_once('model/PostManager.php');
require('model/FormManager.php');
require('model/ComManager.php');
require('model/AccountManager.php');

function listPosts()
{
    $postManager = new PostManager();
    $posts = $postManager->getPosts();

   return $posts;
}

function post()
{
    $postManager = new PostManager();
    $post = $postManager->getPost($_GET['id']);
    return $post;
}

function listComment()
{
    $form = new ComManager();
    $comments = $form->getComments($_GET['id']);
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

function addComment($postId, $pseudo, $comment)
{
    $commentManager = new ComManager();

    $affectedLines = $commentManager->postComment($postId, $pseudo, $comment);

    if ($affectedLines === false) {
        throw new Exception('Impossible d\'ajouter le commentaire !');
    }
    else {

        header('Location: index.php?action=post&id=' . $postId);
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

    $member = $accountManager->connection($_POST['email'], $_POST['password']);

    return $member;

}

