<?php

require_once '../vendor/autoload.php';
require_once '../Model/AdminManager.php';
require_once '../Model/Auth.php';
require_once '../Model/Manager.php';
require_once '../Model/ProjectManager.php';
require_once '../Model/FormManager.php';
require_once '../Model/ComManager.php';
require_once '../vendor/autoload.php';
require_once 'TwigController.php';

use Model\AdminManager;
use Model\Auth;
use Model\Manager;
use Model\ComManager;
use Model\FormManager;
use Model\MemberManager;
use Model\MessageManager;
use Model\ProjectManager;
use Model\SessionManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class TwigController
{
    private $projectManager;

    private $commentManager;

    private $formManager;

    private $memberManager;



    public function __construct()
    {
        $projectManager = new ProjectManager();
        $this->projectManager = $projectManager;

        $commentManager = new ComManager();
        $this->commentManager = $commentManager;

        $formManager = new FormManager();
        $this->formManager = $formManager;

        $memberManager = new MemberManager();
        $this->memberManager = $memberManager;

    }

    public function useTwig($template, array $variables){
        $loader = new FilesystemLoader('../View');
        $twig = new Environment($loader, ['debug' => true]);
        print_r($twig->render($template, $variables));
    }


    public function getSingleTemplate($connection = false, $createAccount = false, $commentForm = false, $resetPasswordForm = false, $id, $error = false, $success = false, $key = false, $newPasswordForm = false)
    {
        $project = $this->projectManager->getProject($id);

        $comments = $this->commentManager->getComments($id);

        if($connection == true){
            $connection = $this->formManager->getConnectionForm();
        }

        if( $createAccount == true) {
            $createAccount = $this->formManager->getCreateAccountForm();
        }

        if($commentForm == true) {
            $commentForm = $this->formManager->getCommentForm();
        }

        if($resetPasswordForm == true) {
            $resetPasswordForm = $this->formManager->getResetPasswordForm();
        }

        if($key == true) {
            $k = $key;
        }

        if($newPasswordForm == true) {
            $newPasswordForm = $this->formManager->getNewPasswordForm();
        }


        $this->useTwig('single.twig', [
            'project' => $project,
            'commentlist' => $comments,
            'error' => $error,
            'success' => $success,
            'connectionform' => $connection,
            'accountform' => $createAccount,
            'commentform' => $commentForm,
            'resetpasswordform' => $resetPasswordForm,
            'key' => $k,
            'newpasswordform' => $newPasswordForm,
        ]);
    }


}