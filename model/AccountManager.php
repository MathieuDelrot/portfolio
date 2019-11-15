<?php


namespace Model;

require_once("model/Manager.php");
require_once("model/FormManager.php");

class AccountManager extends Manager
{

    public function createAccount($first_name, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('INSERT INTO member (first_name, email, password, password_key, key_date) VALUES(?,?,?,0,NULL)');
        $req->execute(array($first_name, $email, $hash));

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
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('SELECT id, password, first_name FROM member WHERE email = ?');
        $req->execute(array($email));
        $data = $req->fetch();
        $first_name = $data['first_name'];
        password_verify($password, $data['password']);
        if(password_verify($password, $data['password'])) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $_SESSION['Auth']= array(
                'first_name' => $first_name,
                'email' => $email,
                'password' => $password
            );
            return true;
        }
        return false;
    }


    public function forgotPassword($email)
    {
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('SELECT id, password, first_name, password_key FROM member WHERE email = ?');
        $req->execute(array($email));
        if ($req->fetch()){
            $password_key = uniqid();
            $req = $bdd->prepare('UPDATE member SET password_key = ?, key_date = NOW() WHERE email = ?');
            $req->execute(array($password_key, $email));
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
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('SELECT key_date FROM member WHERE password_key = ?');
        $req->execute(array($key));
        $date = $req->fetch();
        $d = $date[0];
        $dv = strtotime($d);
        $difference = $vd - $dv;
        if (isset($date) && $difference < 0){
            $req = $bdd->prepare('SELECT * FROM member WHERE password_key = ?');
            $req->execute(array($key));
            if ($req->rowCount() > 0){
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
        $bdd = $this->dbConnect();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $req = $bdd->prepare('UPDATE member SET password = ? WHERE password_key = ?');
        $req->execute(array($hash, $key));
        $req2 = $bdd->prepare('UPDATE member SET password_key = ? WHERE password_key = ?');
        $req2->execute(array('',$key));
        return true;
    }


    public function connectionAdmin($email, $password)
    {
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('SELECT id, password FROM admin WHERE email = ?');
        $req->execute(array($email));
        $data = $req->fetch();
        var_dump($data);
        password_verify($password, $data['password']);
        if(password_verify($password, $data['password'])) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $_SESSION['AuthAdmin']= array(
                'email' => $email,
                'password' => $password
            );
            return true;
        }
        return false;
    }
}