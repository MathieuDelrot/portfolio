<?php


namespace Model;

require_once("model/Manager.php");
require_once("model/FormManager.php");

class AccountManager extends Manager
{


    public function createAccount($first_name_account, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('INSERT INTO member (first_name, email, password) VALUES(?,?, ?)');
        $req->execute(array($first_name_account, $email, $hash));

    }


    public function connection($email, $password)
    {
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('SELECT id, password FROM member WHERE email = ?');
        $req->execute(array($email));
        $data = $req->fetch();
        password_verify($password, $data['password']);
        return $req;

    }

    public function forgotPassword()
    {

    }
}