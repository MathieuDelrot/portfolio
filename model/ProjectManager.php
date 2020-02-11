<?php

namespace Model;

use Entity\MemberEntity;
use Entity\ProjectEntity;

require '../vendor/autoload.php';
require_once 'Manager.php';
require_once  '../Entity/ProjectEntity.php';


class ProjectManager extends Manager
{


    public function createProject(ProjectEntity $project)
    {
        $stmt = $this->bdd->prepare('INSERT INTO portfolio_post (title, slug, content, modification_date, author_id, realisation_date, technologies, url, intro) VALUES(?, ?, ?, NOW(), 1, ?, ?, ?, ?)');
        $stmt->bindParam(1, $project->getTitle());
        $stmt->bindParam(2, $project->getSlug());
        $stmt->bindParam(3, $project->getContent());
        $stmt->bindParam(4, $project->getRealisationDate());
        $stmt->bindParam(5, $project->getTechnologies());
        $stmt->bindParam(6, $project->getUrl());
        $stmt->bindParam(7, $project->getIntro());
        $stmt->execute();
    }

    public function editProject(ProjectEntity $project)
    {
        $stmt = $this->bdd->prepare('UPDATE portfolio_post SET title=?, slug=?, content=?, modification_date=NOW(), author_id=1, realisation_date=?, technologies=?, url=?, intro=? WHERE id = ?');
        $stmt->bindParam(1, $project->getTitle());
        $stmt->bindParam(2, $project->getSlug());
        $stmt->bindParam(3, $project->getContent());
        $stmt->bindParam(4, $project->getRealisationDate());
        $stmt->bindParam(5, $project->getTechnologies());
        $stmt->bindParam(6, $project->getUrl());
        $stmt->bindParam(7, $project->getIntro());
        $stmt->bindParam(8, $project->getId());
        $stmt->execute();
    }

    public function getLastProjects()
    {

        $project = [];

        $q = $this->bdd->query('SELECT * FROM portfolio_post ORDER BY id DESC LIMIT 2');
        $q->execute();

        while ($datas = $q->fetch(\PDO::FETCH_ASSOC))
        {
            $project[] = new ProjectEntity($datas);
        }

        return $project;
    }


    public function getProjects()
    {
        $project = [];

        $q = $this->bdd->query('SELECT * FROM portfolio_post ORDER BY modificationDate DESC');
        $q->execute();

        while ($datas = $q->fetch(\PDO::FETCH_ASSOC))
        {
            $project[] = new ProjectEntity($datas);
        }

        return $project;
    }

    public function getProject($id)
    {
        $stmt = $this->bdd->prepare('SELECT * FROM portfolio_post WHERE id = ?');
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $datas = $stmt->fetch();
        return new ProjectEntity($datas);

    }



}