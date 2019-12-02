<?php

namespace Model;

require_once 'Manager.php';

class ComManager extends Manager
{

public function getComments($postId)
     {
         $comments = $this->bdd->prepare('SELECT pseudo, content, comment_date FROM comment WHERE portfolio_id = ? AND validate = 1');
         $comments->bindParam(1, $postId);
         $comments->execute();

         return $comments;
     }

public function getNewComments()
{
    $comments = $this->bdd->prepare('SELECT * FROM comment WHERE validate = 0');
    $comments->execute();

    return $comments;
}
public function postComment($postId, $first_name, $content)
{
    $stmt = $this->bdd->prepare('INSERT INTO comment (portfolio_id, comment_date, pseudo, content, validate) VALUES(?, NOW(),?, ?, 0)');
    $stmt->bindParam(1, $postId);
    $stmt->bindParam(2, $first_name);
    $stmt->bindParam(3, $content);
    $stmt->execute();
}

public function validComment($id)
{
    $stmt = $this->bdd->prepare('UPDATE comment SET Validate=1 WHERE id= ?');
    $stmt->bindParam(1, $id);
    $stmt->execute();
}

public function deleteComment($id)
{
    $stmt = $this->bdd->prepare('DELETE FROM comment WHERE id= ?');
    $stmt->bindParam(1, $id);
    $stmt->execute();
}


}