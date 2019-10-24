<?php


namespace Model;

require_once("model/Manager.php");

class ComManager extends Manager
{

public function getComments($postId)
     {
         $db = $this->dbConnect();
         $comments = $db->prepare('SELECT pseudo, content, comment_date FROM comment WHERE portfolio_id = ? AND validate = 1');
         $comments->execute(array($postId));

         return $comments;
     }
public function postComment($postId, $pseudo, $content)
{
    $bdd = $this->dbConnect();
    $req = $bdd->prepare('INSERT INTO comment (portfolio_id, comment_date, pseudo, content, validate) VALUES(?, NOW(),?, ?, 0)');
    $req->execute(array($postId, $pseudo, $content));
}


}