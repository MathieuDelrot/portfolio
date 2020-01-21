<?php

namespace App\Model;

require_once 'Manager.php';

class ProjectManager extends Manager
{
    public function createProject($title, $slug, $content, $realisation_date, $technologies, $url, $intro)
    {
        $stmt = $this->bdd->prepare('INSERT INTO portfolio_post (title, slug, content, modification_date, author_id, realisation_date, technologies, url, intro) VALUES(?, ?, ?, NOW(), 1, ?, ?, ?, ?)');
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $slug);
        $stmt->bindParam(3, $content);
        $stmt->bindParam(4, $realisation_date);
        $stmt->bindParam(5, $technologies);
        $stmt->bindParam(6, $url);
        $stmt->bindParam(7, $intro);
        $stmt->execute();
        return true;
    }

    public function editProject($id, $title, $slug, $content, $realisation_date, $technologies, $url, $intro)
    {
        $stmt = $this->bdd->prepare('UPDATE portfolio_post SET title=?, slug=?, content=?, modification_date=NOW(), author_id=1, realisation_date=?, technologies=?, url=?, intro=? WHERE id = ?');
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $slug);
        $stmt->bindParam(3, $content);
        $stmt->bindParam(4, $realisation_date);
        $stmt->bindParam(5, $technologies);
        $stmt->bindParam(6, $url);
        $stmt->bindParam(7, $intro);
        $stmt->bindParam(8, $id);
        $stmt->execute();
        return true;
    }

    public function getLastProjects()
    {
        $req = $this->bdd->query('SELECT * FROM portfolio_post ORDER BY id DESC LIMIT 2');
        return $req;
    }


    public function getProjects()
    {
        $req = $this->bdd->query('SELECT * FROM portfolio_post ORDER BY modification_date DESC');
        return $req;
    }

    public function getProject($id)
    {
        $stmt = $this->bdd->prepare('SELECT * FROM portfolio_post WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $project = $stmt->fetch();
        return $project;
    }



}