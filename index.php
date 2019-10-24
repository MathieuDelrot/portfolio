<?php

use Model\AccountManager;
use Model\Manager;

require('controller/frontend.php');
require ('vendor/autoload.php');



$loader = new \Twig\Loader\FilesystemLoader ('view/frontend');
$twig = new \Twig\Environment($loader, [
    'debug' => true,
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());

try {
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'listPosts') {
            echo $twig->render('home.twig', ['postlist' => listPosts()]);
        }
        elseif ($_GET['action'] == 'post'){
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment()]);
            } else {
                throw new Exception('Aucun identifiant de portfolio envoyé');
            }
        }
        elseif ($_GET['action'] == 'askConnection') {
            if (isset($_POST['email']) and isset($_POST['password'])) {
                askConnection();
                if(askConnection()){
                    echo $twig->render('single.twig', ['post' => post(), 'commentform' => commentForm(), 'commentlist' => listComment(), 'member' => askConnection()]);
                }else{
                    echo 'Mauvais identifiant ou mot de passe !';
                }
                }
            else {
                echo 'Vous n\'avez pas renseigné toutes les données';
            }
        }
        elseif ($_GET['action'] == 'addComment') {
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                if (!empty($_GET['id']) && !empty($_POST['comment'])) {
                    addComment($_GET['id'], $_POST['pseudo'], $_POST['comment']);
                } else {
                    throw new Exception('Tous les champs ne sont pas remplis !');
                }
            } else {
                throw new Exception('Aucun identifiant de billet envoyé');
            }
        }
        elseif ($_GET['action'] == 'contact'){
            echo $twig->render('contact.twig', ['contactform' => form()]);
        }
        elseif ($_GET['action'] == 'createAccount'){
            echo $twig->render('create-account.twig', ['accountform' => accountForm()]);
        }
        elseif ($_GET['action'] == 'addAccount') {
            if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email'])){
                $format = "Format d'email erroné";
                echo $twig->render('create-account.twig', [
                    'accountform' => accountForm(),
                    'format' => $format,
                ]);
            }
            elseif (!empty($_POST['first_name_account']) && !empty($_POST['email'] && !empty($_POST['password']))) {
                $manager = new Manager();
                $bdd = $manager->dbConnect();
                $stmt = $bdd->prepare('SELECT * FROM member WHERE email=?');
                $stmt->bindValue( 1, $_POST['email'] );
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $exist = "Email déjà existant";
                    echo $twig->render('create-account.twig', [
                        'accountform' => accountForm(),
                        'exist' => $exist,
                    ]);
                }
                else{
                    addAccount($_POST['first_name_account'], $_POST['email'], $_POST['password']);
                    echo $twig->render('account-creation-asked.twig',[
                        'first_name_account' => $_POST['first_name_account']
                    ]);
                }
            } else {
                throw new Exception('Tous les champs ne sont pas remplis !');
            }
        }
        }
    else {
        echo $twig->render('home.twig', ['postlist' => listPosts()]);
    }
}
catch(Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
