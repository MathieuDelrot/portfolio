<?php

Class FormManager{

    private $data;

    public function __construct($data = array())
    {
        $this->data = $data;
    }

    public function input($firstname){
        return '<p><input type="text" name=" . $firstname . "></p>';
        }

    public function submit(){
        return '<p><button type="submit">Envoyer</button></p>';
    }

}
