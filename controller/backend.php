<?php


use Model\AccountManager;
use Model\ComManager;
use Model\FormManager;
use Model\Manager;
use Model\PostManager;
use Model\Logout;
use Model\Auth;

require_once '../model/Manager.php';
require_once '../model/PostManager.php';
require_once '../model/FormManager.php';
require_once '../model/ComManager.php';
require_once '../model/AccountManager.php';
require_once '../vendor/autoload.php';
require_once 'TwigController.php';


function getAdminConnectionForm()
{
    $form = new FormManager();
    $admin_connection_form = $form->getAdminConnectionForm();

    return $admin_connection_form;
}

function getAdminConnection()
{
     if (Auth::adminIsLogged()) {
         $success_connection = 'Vous pouvez ajouter des portfolios et modérer les commentaires';
         useTwig('homeAdmin.twig', [
             'success_connection' => $success_connection
         ]);
     } else {
         useTwig('homeAdmin.twig', [
             'adminconnectionform' => getAdminConnectionForm()
         ]);
     }
}