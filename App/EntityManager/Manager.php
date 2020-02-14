<?php

namespace App\EntityManager;

use Config\Config;

class Manager
{

    protected $bdd;

    public function __construct()
    {
        $this->bdd = $this->dbConnect();
    }

    public function dbConnect()
    {
        $config = new Config();

        $db = new \PDO('mysql:host='.$config->getDbHost().';dbname='.$config->getDbName().';charset=utf8', $config->getDbUser(), $config->getDbPass());
        return $db;
    }


}