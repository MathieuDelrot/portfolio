<?php

namespace Model;

require_once("model/Manager.php");

class PostManager extends Manager
{
    public function post($title, $content, $realisation_date, $technologies, $url, $intro)
    {
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('INSERT INTO portfolio_post (title, content, modification_date, author_id, status, realisation_date, technologies, url, intro) VALUES(?, ?, NOW(), 1, 1, ?, ?, ?, ?)');
        $req->execute(array($title, $content, $realisation_date, $technologies, $url, $intro));
    }

    public function editPost ($id, $title, $content, $realisation_date, $technologies, $url, $intro)
    {
        $bdd = $this->dbConnect();
        $req = $bdd->prepare('UPDATE portfolio_post SET title=?, content=?, modification_date=NOW(), author_id=1, status=1, realisation_date=?, technologies=?, url=?, intro=? WHERE id = ?');
        $req->execute(array($title, $content, $realisation_date, $technologies, $url, $intro, $id));
    }


    public function getPosts()
    {
        $db = $this->dbConnect();
        $req = $db->query('SELECT * FROM portfolio_post ORDER BY modification_date DESC LIMIT 0, 5');

        return $req;
    }

    public function getPost($id)
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT * FROM portfolio_post WHERE id = ?');
        $req->execute(array($id));
        $post = $req->fetch();
        return $post;
    }
}