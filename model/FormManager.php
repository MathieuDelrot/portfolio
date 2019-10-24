<?php


namespace Model;

require_once("model/Manager.php");
require_once("model/PostManager.php");



class FormManager extends Manager
{
    private $data;


    public function __construct($data = array())
    {
        $this->data = $data;
    }

    public function input($type, $name, $placeholder)
    {
        return "<p><input type='" . $type . "' name='" . $name . "'  placeholder='" . $placeholder . "' required></p>";
    }

    public function textarea($name, $placehoder)
    {
        return "<p><textarea name='" . $name . "' placeholder='" . $placehoder . "' required></textarea></p>";
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

    public function addMessage()
    {
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('INSERT INTO message (first_name, last_name, email, textarea) VALUES(?, ?, ?, ?)');
        $req->execute(array($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['message']));
    }

    public function getCommentForm()
    {
        $comment_form = array(
            "pseudo" => $this->input('text', 'pseudo', 'Pseudo'),
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

}

