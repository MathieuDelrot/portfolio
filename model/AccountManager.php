<?php

namespace Model;

require_once 'Manager.php';
require_once 'FormManager.php';

class AccountManager extends Manager
{


    public function createAccount($first_name, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->bdd->prepare('INSERT INTO member (first_name, email, password, password_key, key_date) VALUES(?,?,?,0,NULL)');
        $stmt->bindParam(1, $first_name);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $hash);
        $stmt->execute();

    }

    public function sendEmailSuccess($first_name, $email)
    {
        $to      = $email;
        $subject = 'Votre compte à été créé avec succès';
        $message = 'Bonjour '. $first_name .' vous pouvez désormais rédiger des commentaires sur les portfolios du site www.mathieu-delrot.fr';
        $headers = 'From: contact@mathieu-delrot.fr' . "\r\n" .
            'Reply-To: contact@mathieu-delrot.fr' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


        mail($to, $subject, $message, $headers);
    }

    public function sendEmailResetPassword($email, $key)
    {
        //Mettre le message dans un template
        $to      = $email;
        $subject = 'Vous souhaitez réinitialiser votre mot de passe';
        $message = '\'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                </head>
                <body>
                
                    <div>
                            <p>Vous souhaitez réinitialiser votre mot de passe</p>
                            <p>"<a href ="https://www.mathieu-delrot.fr/index.php?action=resetPassword&id=1&key=' . $key . '">Cliquer sur le lien :</a>"</p>
                    
                    </div>
                </body>
                </html>\'';

        $headers = 'From: contact@mathieu-delrot.fr' . "\r\n" .
            'Reply-To: contact@mathieu-delrot.fr' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


        mail($to, $subject, $message, $headers);
    }



    public function connection($email, $password)
    {
        $stmt = $this->bdd->prepare('SELECT id, password, first_name FROM member WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $data = $stmt->fetch();
        $first_name = $data['first_name'];
        password_verify($password, $data['password']);
        if(password_verify($password, $data['password'])) {
            return true;
        }
        return false;
    }


    public function forgotPassword($email)
    {
        $stmt = $this->bdd->prepare('SELECT id, password, first_name, password_key FROM member WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        if ($stmt->fetch()){
            $password_key = uniqid();
            $stmt = $this->bdd->prepare('UPDATE member SET password_key = ?, key_date = NOW() WHERE email = ?');
            $stmt->bindParam(1, $password_key);
            $stmt->bindParam(2, $email);
            $stmt->execute();
            $this->sendEmailResetPassword($email, $password_key);
            return true;
        }else{
            return false;
        }
    }


    public function findPasswordKey($key)
    {
        $validityDate = new \DateTime();
        $validityDate->modify('-24 hours');
        $v = $validityDate->format('Y-m-d H:i:s');
        $vd = strtotime($v);
        $stmt = $this->bdd->prepare('SELECT key_date FROM member WHERE password_key = ?');
        $stmt->bindParam(1, $key);
        $stmt->execute();
        $date = $stmt->fetch();
        $d = $date[0];
        $dv = strtotime($d);
        $difference = $vd - $dv;
        if (isset($date) && $difference < 0){
            $stmt = $this->bdd->prepare('SELECT * FROM member WHERE password_key = ?');
            $stmt->bindParam(1, $key);
            $stmt->execute();
            if ($stmt->rowCount() > 0){
                return true;
            } else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function changePassword($password, $key)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->bdd->prepare('UPDATE member SET password = ? WHERE password_key = ?');
        $stmt->bindParam(1, $hash);
        $stmt->bindParam(2, $key);
        $stmt->execute();
        $stmt2 = $this->bdd->prepare('UPDATE member SET password_key = ? WHERE password_key = ?');
        $stmt2->bindParam(1, '');
        $stmt2->bindParam(2, $key);
        $stmt2->execute();
        return true;
    }


    public function connectionAdmin($email, $password)
    {
        $stmt = $this->bdd->prepare('SELECT id, password FROM admin WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $data = $stmt->fetch();
        //refactoriser avec des cookies
        $ifAuthentificated = password_verify($password, $data['password']);
        if($ifAuthentificated) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $_SESSION['AuthAdmin']= array(
                'email' => $email,
                'password' => $password
            );
        }
        return $ifAuthentificated;
    }
}