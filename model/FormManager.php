<?php

namespace Model;

require_once 'Manager.php';
require_once 'PostManager.php';

class FormManager extends Manager
{
    private $data;


    public function __construct($data = array())
    {
        $this->data = $data;
    }

    public function input($type, $name, $placeholder, $value = "")
    {
        return "<p><input type='" . $type . "' name='" . $name . "'  placeholder='" . $placeholder . "' value='" . $value . "' required></p>";
    }

    public function textarea($name, $placehoder,  $value = "")
    {
        return "<p><textarea name='" . $name . "' placeholder='" . $placehoder . "'  required>$value</textarea></p>";
    }

    public function submit()
    {
        return "<p><button type='submit'>Envoyer</button></p>";
    }


    public function getContactForm()
    {
        $contact_form = array(
            "first_name" => $this->input('text', 'firstname', 'Prénom'),
            "last_name" => $this->input('text', 'lastname', 'Nom'),
            "email" => $this->input('email', 'email', 'E-mail'),
            "textarea" => $this->textarea('message', 'Votre message'),
            "submit" => $this->submit()
        );
        return $contact_form;

    }

    public function getPostForm()
    {

        $post_form = array(
            "title" => $this->input('text', 'title', 'Titre'),
            "intro" => $this->textarea('intro', 'Introduction'),
            "url" => $this->input('text', 'url', 'URL du projet'),
            "technologies" => $this->input('text', 'technologies', 'Languages web'),
            "realisation_date" => $this->input('text', 'realisation_date', 'Mois de mise en ligne'),
            "content" => $this->textarea('content', 'Présentation du projet'),
            "submit" => $this->submit()
        );

        return $post_form;
    }

    public function getEditPostForm($id,$title, $content, $realisation_date, $technologies, $url, $intro)
    {

        $post_form = array(
            "id" => $this->input('hidden', 'id', 'id', $id),
            "title" => $this->input('text', 'title', 'Titre', $title),
            "intro" => $this->textarea('intro', 'Introduction', $intro),
            "url" => $this->input('text', 'url', 'URL du projet', $url),
            "technologies" => $this->input('text', 'technologies', 'Languages web', $technologies),
            "realisation_date" => $this->input('text', 'realisation_date', 'Mois de mise en ligne', $realisation_date),
            "content" => $this->textarea('content', 'Présentation du projet', $content),
            "submit" => $this->submit()
        );

        return $post_form;
    }


    public function getCommentForm()
    {
        $comment_form = array(
            "comment" => $this->textarea('comment', 'Votre commentaire'),
            "submit" => $this->submit()
        );
        return $comment_form;
    }

    public function getCreateAccountForm()
    {

        $account_form = array(
            "first_name_account" => $this->input('text', 'first_name_account', 'Prénom'),
            "email" => $this->input('email', 'email', 'Votre e-mail'),
            "password" => $this->input('password', 'password', 'Votre mots de passe'),
            "submit" => $this->submit()
        );

        return $account_form;
    }


    public function getConnectionForm(){

        $connection_form = array(
            "first_name_account" => $this->input('text', 'first_name_account', 'Prénom'),
            "email" => $this->input('email', 'email', 'Votre e-mail'),
            "password" => $this->input('password', 'password', 'Votre mots de passe'),
            "submit" => $this->submit()
        );

        return $connection_form;
    }

    public function getResetPasswordForm(){

        $reset_password_form = array(
            "email" => $this->input('email', 'email', 'Votre e-mail'),
            "submit" => $this->submit()
        );

        return $reset_password_form;
    }

    public function getNewPasswordForm(){
        $new_password_form = array(
            "password" => $this->input('password', 'password', 'Votre mots de passe'),
            "submit" => $this->submit()
        );

        return $new_password_form;
    }

    public function getAdminConnectionForm(){

        $connection_form = array(
            "email" => $this->input('email', 'email', 'Votre e-mail'),
            "password" => $this->input('password', 'password', 'Votre mots de passe'),
            "submit" => $this->submit()
        );

        return $connection_form;
    }

}

