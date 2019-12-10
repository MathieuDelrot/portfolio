<?php


namespace Model;

require_once 'Manager.php';

class AdminManager extends Manager
{

    public function adminConnection($email, $password)
    {
        $stmt = $this->bdd->prepare('SELECT id, password FROM admin WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $data = $stmt->fetch();
        $ifAuthentificated = password_verify($password, $data['password']);
        if($ifAuthentificated) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $session = new SessionManager();
            $session->vars['AuthAdmin'] = array(
                'email' => $email,
                'password' => $password
            );
        }
        return $ifAuthentificated;
    }


}