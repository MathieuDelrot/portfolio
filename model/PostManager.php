<?php

namespace Model;

require_once 'Manager.php';

class PostManager extends Manager
{

    public function post($title, $content, $realisation_date, $technologies, $url, $intro)
    {
        $stmt = $this->bdd->prepare('INSERT INTO portfolio_post (title, content, modification_date, author_id, status, realisation_date, technologies, url, intro) VALUES(?, ?, NOW(), 1, 1, ?, ?, ?, ?)');
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $content);
        $stmt->bindParam(3, $realisation_date);
        $stmt->bindParam(4, $technologies);
        $stmt->bindParam(5, $url);
        $stmt->bindParam(6, $intro);
        $stmt->execute();
    }

    public function editPost ($id, $title, $content, $realisation_date, $technologies, $url, $intro)
    {

        $stmt = $this->bdd->prepare('UPDATE portfolio_post SET title=?, content=?, modification_date=NOW(), author_id=1, status=1, realisation_date=?, technologies=?, url=?, intro=? WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $title);
        $stmt->bindParam(3, $content);
        $stmt->bindParam(4, $realisation_date);
        $stmt->bindParam(5, $technologies);
        $stmt->bindParam(6, $url);
        $stmt->bindParam(7, $intro);
        $stmt->execute();
    }


    public function getPosts()
    {
        $req = $this->bdd->query('SELECT * FROM portfolio_post ORDER BY modification_date DESC');
        return $req;
    }

    public function getPost($id)
    {
        $stmt = $this->bdd->prepare('SELECT * FROM portfolio_post WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $post = $stmt->fetch();
        return $post;
    }
}