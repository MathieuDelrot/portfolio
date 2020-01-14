<?php


namespace Model;

require_once 'Manager.php';


class MessageManager extends Manager
{

    public function addMessage($firstName, $lastName, $email, $message)
    {
        $stmt = $this->bdd->prepare('INSERT INTO message (first_name, last_name, email, message) VALUES(?, ?, ?, ?)');
        $stmt->bindParam(1, $firstName);
        $stmt->bindParam(2, $lastName);
        $stmt->bindParam(3, $email);
        $stmt->bindParam(4, $message);
        $stmt->execute();
        $this->sendMessage($firstName, $lastName, $email, $message);
    }
    public function sendMessage($firstName, $lastName, $email, $message)
    {
        //Mettre le message dans un template
        $message = '\'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                </head>
                <body>

                    <div>
                            <p>Un message vient du site web :/p>
                            <p>' . $firstName . '</p>
                            <p>' . $lastName . '</p>
                            <p>' . $email . '</p>
                            <p>' . $message . '</p>

                    </div>
                </body>
                </html>\'';

        $headers = 'From: mathieu' . "\r\n" .
            'Reply-To: '. $email . "\r\n" .
            'X-Mailer: PHP/' . phpversion();


        mail('mathieuhdb@gmail.com', 'Contact depuis le site web Mathieu DELROT', $message, $headers);
    }

}