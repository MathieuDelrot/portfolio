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

public function getNewComments()
{
    $db = $this->dbConnect();
    $comments = $db->prepare('SELECT * FROM comment WHERE validate = 0');
    $comments->execute(array());

    return $comments;
}
public function postComment($postId, $first_name, $content)
{
    $bdd = $this->dbConnect();
    $req = $bdd->prepare('INSERT INTO comment (portfolio_id, comment_date, pseudo, content, validate) VALUES(?, NOW(),?, ?, 0)');
    $req->execute(array($postId, $first_name, $content));
}

public function validComment($id)
{
    $bdd = $this->dbConnect();
    $req = $bdd->prepare('UPDATE comment SET Validate=1 WHERE id= ?');
    $req->execute(array($id));
}

public function deleteComment($id)
{
    $bdd = $this->dbConnect();
    $req = $bdd->prepare('DELETE FROM comment WHERE id= ?');
    $req->execute(array($id));
}


}