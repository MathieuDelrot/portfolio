<?php

namespace App\EntityManager;

use App\Entity\CommentEntity;

class ComManager extends Manager
{

    public function addComment(CommentEntity $comment, $id)
    {
        $pseudo = $comment->getPseudo();
        $content = $comment->getContent();

        $stmt = $this->bdd->prepare('INSERT INTO comment (projectId, commentDate, pseudo, content, validate) VALUES(?, NOW(),?, ?, 0)');
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $pseudo);
        $stmt->bindParam(3, $content);
        $stmt->execute();
    }

    public function validComment($id)
    {
        $stmt = $this->bdd->prepare('UPDATE comment SET validate=1 WHERE id= ?');
        $stmt->bindParam(1, $id);
        $stmt->execute( );
    }

    public function deleteComment($id)
    {
        $stmt = $this->bdd->prepare('DELETE FROM comment WHERE id= ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
    }

    public function getComments($id)
    {
        $comment = [];

        $q = $this->bdd->prepare('SELECT pseudo, content, commentDate FROM comment WHERE projectId = ? AND validate = 1 ORDER BY id DESC');
        $q->bindParam(1, $id);
        $q->execute();

        while ($datas = $q->fetch(\PDO::FETCH_ASSOC))
        {
            $comment[] = new CommentEntity($datas);
        }
        return $comment;

    }

    public function getNewComments()
    {
        $comment = [];

        $q = $this->bdd->prepare('SELECT * FROM comment WHERE validate = 0 ORDER BY id DESC');
        $q->execute();

        while ($datas = $q->fetch(\PDO::FETCH_ASSOC))
        {
            $comment[] = new CommentEntity($datas);
        }
        return $comment;

    }

}