<?php

namespace App\EntityManager;

use App\Entity\ProjectEntity;

class ProjectManager extends Manager
{

    public function createProject(ProjectEntity $project)
    {
        $title = $project->getTitle();
        $slug = $project->getSlug();
        $content = $project->getContent();
        $realisationDate = $project->getRealisationDate();
        $technologies = $project->getTechnologies();
        $url = $project->getUrl();
        $intro = $project->getIntro();

        $stmt = $this->bdd->prepare('INSERT INTO portfolio_post (title, slug, content, modificationDate, authorId, realisationDate, technologies, url, intro) VALUES(?, ?, ?, NOW(), 1, ?, ?, ?, ?)');
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $slug);
        $stmt->bindParam(3, $content);
        $stmt->bindParam(4, $realisationDate);
        $stmt->bindParam(5, $technologies);
        $stmt->bindParam(6, $url);
        $stmt->bindParam(7, $intro);
        $req = $stmt->execute();
        return $req;

    }

    public function editProject(ProjectEntity $project)
    {
        $title = $project->getTitle();
        $slug = $project->getSlug();
        $content = $project->getContent();
        $realisationDate = $project->getRealisationDate();
        $technologies = $project->getTechnologies();
        $url = $project->getUrl();
        $intro = $project->getIntro();
        $id = $project->getId();

        $stmt = $this->bdd->prepare('UPDATE portfolio_post SET title=?, slug=?, content=?, modificationDate=NOW(), authorId=1, realisationDate=?, technologies=?, url=?, intro=? WHERE id = ?');
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $slug);
        $stmt->bindParam(3, $content);
        $stmt->bindParam(4, $realisationDate);
        $stmt->bindParam(5, $technologies);
        $stmt->bindParam(6, $url);
        $stmt->bindParam(7, $intro);
        $stmt->bindParam(8, $id);
        $req = $stmt->execute();
        return $req;


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