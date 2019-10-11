<?php
require('model/FormManager.php');


function saveMessage()
{
    $save_form = new FormManager();
    $contact_form = $save_form->addMessage();
    return $contact_form;
}


