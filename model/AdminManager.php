<?php


namespace Model;

use Entity\AdminEntity;

require_once 'Manager.php';
require_once  '../Entity/AdminEntity.php';

class AdminManager extends Manager
{

    public function adminConnection(AdminEntity $admin)
    {
        $stmt = $this->bdd->prepare('SELECT id, password FROM admin WHERE email = ?');
        $stmt->bindParam(1, $admin->getEmail());
        $stmt->execute();
        $data = $stmt->fetch();
        $ifAuthentificated = password_verify($admin->getPassword(), $data['password']);
        if($ifAuthentificated) {
            $password = password_hash($admin->getPassword(), PASSWORD_DEFAULT);
            $session = new SessionManager();
            $session->vars['AuthAdmin'] = array(
                'email' => $admin->getEmail(),
                'password' => $password
            );
        }
        return $ifAuthentificated;
    }


}