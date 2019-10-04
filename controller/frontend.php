<?php

// Chargement des classes
require_once('model/PostManager.php');
require('model/FormManager.php');

function listPosts()
{
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $posts = $postManager->getPosts();

   return $posts;
}

function post()
{
    $postManager = new \OpenClassrooms\Blog\Model\PostManager();
    $post = $postManager->getPost($_GET['id']);

    return $post;
}

function form()
{
    $form = new FormManager();
    echo $form->input('firstname');
    echo $form->input('lastname');
    echo $form->submit();
}
