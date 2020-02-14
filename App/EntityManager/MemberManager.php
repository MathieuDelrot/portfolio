<?php

namespace App\EntityManager;

use App\Entity\MemberEntity;
use App\Helper\SessionHelper;

class MemberManager extends Manager
{


    public function createAccount(MemberEntity $member)
    {
        $firstName = $member->getFirstName();
        $email = $member->getEmail();

        $hash = password_hash($member->getPassword(), PASSWORD_DEFAULT);
        $stmt = $this->bdd->prepare('INSERT INTO member (firstName, email, password, passwordKey, keyDate, validate) VALUES(?,?,?,0,NULL,0)');
        $stmt->bindParam(1, $firstName);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $hash);
        $stmt->execute();
        return $stmt;
    }


    public function validAccount(MemberEntity $member)
    {
        $id = $member->getId();

        $stmt = $this->bdd->prepare('UPDATE member SET validate=1 WHERE id= ?');
        $stmt->bindParam(1, $id);
        $stmt->execute( );
        $this->sendEmailSuccess($id);

    }

    public function deleteAccount(MemberEntity $member)
    {
        $id = $member->getId();

        $stmt = $this->bdd->prepare('DELETE FROM member WHERE id= ?');
        $stmt->bindParam(1, $id);
        $stmt->execute( );
        $this->sendEmailSuccess($id);
    }

    public function sendEmailSuccess($id)
    {
        $stmt = $this->bdd->prepare('SELECT email, firstName FROM member WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $member = $stmt->fetch();
        $email = $member['email'];
        $first_name = $member['firstName'];
        $to      = $email;
        $subject = 'Votre compte a ete cree avec succes';
        $message = 'Bonjour '. $first_name .' vous pouvez désormais rédiger des commentaires sur les portfolios du site www.mathieu-delrot.fr';
        $headers = 'From: contact@mathieu-delrot.fr' . "\r\n" .
            'Reply-To: contact@mathieu-delrot.fr' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


        mail($to, $subject, $message, $headers);
    }


    public function getNewMember()
    {
        $member = [];

        $q = $this->bdd->prepare('SELECT * FROM member WHERE validate = 0 ORDER BY id DESC');
        $q->execute();

        while ($datas = $q->fetch(\PDO::FETCH_ASSOC))
        {
            $member[] = new MemberEntity($datas);
        }
        return $member;
    }


    public function connection(MemberEntity $member)
    {
        $email = $member->getEmail();
        $stmt = $this->bdd->prepare('SELECT id, password, firstName FROM member WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $data = $stmt->fetch();
        if ($data != null) {
            $firstName = $data['firstName'];
            password_verify($member->getPassword(), $data['password']);
            if (password_verify($member->getPassword(), $data['password'])) {
                $password = password_hash($member->getPassword(), PASSWORD_DEFAULT);
                $session = new SessionHelper();
                $session->vars['AuthHelper'] = array(
                    'email' => $email,
                    'firstName' => $firstName,
                    'password' => $password
                );
                return true;
            }
        } else {
            return false;
        }
    }

    public function forgotPassword(MemberEntity $member, $id, $slug)
    {
        $email = $member->getEmail();
        $uniqKey = uniqid();
        $member->setPasswordKey($uniqKey);
        $passwordKey = $member->getPasswordKey();

        $stmt = $this->bdd->prepare('SELECT id, password, firstName, passwordKey FROM member WHERE email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        if ($stmt->fetch()){
            $member->setPasswordKey(uniqid());
            $stmt = $this->bdd->prepare('UPDATE member SET passwordKey = ?, keyDate = NOW() WHERE email = ?');
            $stmt->bindParam(1, $passwordKey);
            $stmt->bindParam(2, $email);
            $stmt->execute();
            $this->sendEmailResetPassword($email, $passwordKey, $slug, $id);
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
        $to      = $email;
        $subject = 'Vous souhaitez reinitialiser votre mot de passe';
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

    public function changePassword(MemberEntity $member, $key)
    {
        $password = $member->getPassword();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->bdd->prepare('UPDATE member SET password = ? WHERE passwordKey = ?');
        $stmt->bindParam(1, $hash);
        $stmt->bindParam(2, $key);
        $st = $stmt->execute();
        return $st;
    }


}