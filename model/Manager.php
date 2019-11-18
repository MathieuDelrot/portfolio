<?php

namespace Model;

class Manager
{
    public function dbConnect()
    {
        $db = new \PDO('mysql:host=localhost;dbname=my_portfolio;charset=utf8', 'root', 'root');
        return $db;
    }

}