<?php

namespace Model;


class Manager
{
    protected $bdd;

    public function __construct()
    {
        $this->bdd = $this->dbConnect();
    }

    public function dbConnect()
    {
        $db = new \PDO('mysql:host=localhost;dbname=my_portfolio;charset=utf8', 'root', 'root');
        return $db;
    }


}