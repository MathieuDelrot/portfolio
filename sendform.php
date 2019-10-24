<?php

use Model\FormManager;

require('model/FormManager.php');

$save_form = new FormManager();
$save_form->addMessage($_GET['id']);

header('Location: index.php?action=contact');
exit;


