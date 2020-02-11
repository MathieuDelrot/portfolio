<?php


namespace Model;

use Entity\MemberEntity;

require_once  '../Entity/MemberEntity.php';
require_once 'Manager.php';
require_once 'FormManager.php';
require_once 'SessionManager.php';


class MemberManager extends Manager
{


    public function createAccount(MemberEntity $member)
    {
        $hash = password_hash($member->getPassword(), PASSWORD_DEFAULT);
        $stmt = $this->bdd->prepare('INSERT INTO member (firstName, email, password, passwordKey, keyDate, validate) VALUES(?,?,?,0,NULL,0)');
        $stmt->bindParam(1, $member->getFirstName());
        $stmt->bindParam(2, $member->getEmail());
        $stmt->bindParam(3, $hash);
        $stmt->execute();
        return $stmt;
    }


    public function validAccount(MemberEntity $member)
    {
        $stmt = $this->bdd->prepare('UPDATE member SET validate=1 WHERE id= ?');
        $stmt->bindParam(1, $member->getId());
        $stmt->execute( );
        $this->sendEmailSuccess($member->getId());

    }

    public function deleteAccount(MemberEntity $member)
    {
        $stmt = $this->bdd->prepare('DELETE FROM member WHERE id= ?');
        $stmt->bindParam(1, $member->getId());
        $stmt->execute( );
        $this->sendEmailSuccess($member->getId());
    }

    public function sendEmailSuccess($id)
    {
        $stmt = $this->bdd->prepare('SELECT email, firstName FROM member WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $member = $stmt->fetch();
        $email = $member['email'];
        $first_name = $member['first_name'];
        $to      = $email;
        $subject = 'Votre compte à été créé avec succès';
        $message = 'Bonjour '. $first_name .' vous pouvez désormais rédiger des commentaires sur les portfolios du site www.mathieu-delrot.fr';
        $headers = 'From: contact@mathieu-delrot.fr' . "\r\n" .
            'Reply-To: contact@mathieu-delrot.fr' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


        mail($to, $subject, $message, $headers);
    }


    public function getNewMember()
    {
        $stmt = $this->bdd->prepare('SELECT * FROM member WHERE validate = 0 ORDER BY id DESC');
        $stmt->execute();
        return $stmt;
    }


    public function connection(MemberEntity $member)
    {
        $stmt = $this->bdd->prepare('SELECT id, password, firstName FROM member WHERE email = ?');
        $stmt->bindParam(1, $member->getEmail());
        $stmt->execute();
        $data = $stmt->fetch();
        $first_name = $data['first_name'];
        password_verify($member->getPassword(), $data['password']);
        if(password_verify($member->getPassword(), $data['password'])) {
            $password = password_hash($member->getPassword(), PASSWORD_DEFAULT);
            $session = new SessionManager();
            $session->vars['Auth'] = array(
                'email' => $member->getEmail(),
                'first_name' => $first_name,
                'password' => $password
            );
            return true;
        }
        return false;
    }

    public function forgotPassword(MemberEntity $member, $id, $slug)
    {
        $stmt = $this->bdd->prepare('SELECT id, password, firstName, passwordKey FROM member WHERE email = ?');
        $stmt->bindParam(1, $member->getEmail());
        $stmt->execute();
        if ($stmt->fetch()){
            $member->__set('passwordKey', uniqid());
            $stmt = $this->bdd->prepare('UPDATE member SET passwordKey = ?, keyDate = NOW() WHERE email = ?');
            $stmt->bindParam(1, $member->getPasswordKey());
            $stmt->bindParam(2, $member->getEmail());
            $stmt->execute();
            $this->sendEmailResetPassword($member->getEmail(), $member->getPasswordKey(), $slug, $id);
            return true;
        }else{
            return false;
        }
    }

    public function findPasswordKey(MemberEntity $member)
    {
        $validityDate = new \DateTime();
        $validityDate->modify('-24 hours');
        $v = $validityDate->format('Y-m-d H:i:s');
        $vd = strtotime($v);
        $stmt = $this->bdd->prepare('SELECT keyDate FROM member WHERE passwordKey = ?');
        $stmt->bindParam(1, $key);
        $stmt->execute();
        $date = $stmt->fetch();
        $d = $date[0];
        $dv = strtotime($d);
        $difference = $vd - $dv;
        if (isset($date) && $difference < 0){
            $stmt = $this->bdd->prepare('SELECT * FROM member WHERE passwordKey = ?');
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



    public function sendEmailResetPassword($email, $key, $slug, $id)
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
                            <p>"<a href ="https://www.mathieu-delrot.fr/' . $slug . '-' . $id . '/reinitialiser-mot-de-passe/' . $key . '">Cliquer ici</a>"</p>
                    
                    </div>
                </body>
                </html>\'';

        $headers = 'From: contact@mathieu-delrot.fr' . "\r\n" .
            'Reply-To: contact@mathieu-delrot.fr' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


        mail($to, $subject, $message, $headers);
    }

    public function changePassword($password, $key)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->bdd->prepare('UPDATE member SET password = ? WHERE password_key = ?');
        $stmt->bindParam(1, $hash);
        $stmt->bindParam(2, $key);
        $stmt->execute();
    }


}