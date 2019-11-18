<?php

use Model\FormManager;

require('model/FormManager.php');

$save_form = new FormManager();
$save_form->addMessage(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));

header('Location: index.php?action=contact');
exit;


