<?php

use Model\AccountManager;
use Model\Auth;
use Model\Logout;
use Model\Manager;

require('controller/frontend.php');
require ('vendor/autoload.php');
require ('model/Auth.php');


$loader = new \Twig\Loader\FilesystemLoader('view');
$twig = new \Twig\Environment($loader, [
    'debug' => true,
]);
$twig->addExtension(new Twig_Extension_Session());
$twig->addExtension(new \Twig\Extension\DebugExtension());
session_start();

try {
    if (get('listPosts')) {
        echo $twig->render('home.twig', ['postlist' => listPosts()]);
    } elseif (get('post')) {
        if (isset($_GET['id']) && $_GET['id'] > 0 && Auth::isLogged()) {
            echo $twig->render('single.twig', ['post' => post(), 'commentform' => commentForm(), 'commentlist' => listComment()]);
        } elseif (isset($_GET['id']) && $_GET['id'] > 0) {
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'accountform' => accountForm()]);
        } else {
            throw new Exception('Aucun identifiant de portfolio envoyé');
        }
    } elseif (get('addAccount')) {
        if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email'])) {
            $error_inscription = "Format d'email erroné";
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'accountform' => accountForm(), 'error_inscription' => $error_inscription]);
        } elseif (!empty($_POST['first_name_account']) && !empty($_POST['email'] && !empty($_POST['password']))) {
            $manager = new Manager();
            $bdd = $manager->dbConnect();
            $stmt = $bdd->prepare('SELECT * FROM member WHERE email=?');
            $stmt->bindValue(1, $_POST['email']);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $error_inscription = "Format d'email erroné";
                echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'accountform' => accountForm(), 'error_inscription' => $error_inscription]);
            } else {
                addAccount($_POST['first_name_account'], $_POST['email'], $_POST['password']);
                $success_inscription = 'Votre compte est créé vous pouvez laisser des commentaires';
                echo $twig->render('single.twig', ['post' => post(), 'commentform' => commentForm(), 'commentlist' => listComment(), 'success_inscription' => $success_inscription]);
            }
        } else {
            throw new Exception('Tous les champs ne sont pas remplis !');
        }
    } elseif (get('askConnection')) {
        if (isset($_POST['email']) and isset($_POST['password'])) {
            if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email'])) {
                $error_connection = "Format d'email erroné";
                echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'member' => askConnection(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
            } else {
                askConnection();
                if ((!empty($_POST['email']) && !empty($_POST['password']) && askConnection()) or Auth::adminIsLogged()) {
                    $success_connection = 'Vous êtes connecté vous pouvez laisser des commentaires';
                    echo $twig->render('single.twig', ['post' => post(), 'commentform' => commentForm(), 'commentlist' => listComment(), 'success_connection' => $success_connection]);
                } else {
                    $error_connection = 'Mauvais identifiant ou mot de passe !';
                    echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'member' => askConnection(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
                }
            }
        } else {
            throw new Exception('Vous n\'avez pas renseigné toutes les données');
        }
    } elseif (get('forgetPassword')) {
        echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm()]);
    } elseif (get('askNewPassword')) {
        askNewPassword();
        if (!empty($_POST['email']) && askNewPassword() == true) {
            $success_connection = 'Vous allez recevoir un email pour réinitialiser votre mot de passe';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'success_connection' => $success_connection]);
        } else {
            $error_connection = 'Votre e-mail n\'est pas reconnu';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
        }
    } elseif (get('resetPassword')) {
        checkIfPasswordKeyExist();
        if ($_GET['key'] && checkIfPasswordKeyExist() == true) {
            $success_connection = 'Renseignez votre nouveau mot de passe';
            echo $twig->render('single.twig', ['post' => post(), 'newpasswordform' => newPasswordForm(), 'key' => $_GET['key'], 'success_connection' => $success_connection]);
        } else {
            $error_connection = 'Votre lien n\'est pas reconnu ou n\'est plus valide';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
        }
    } elseif (get('resetPassword')) {
        changePassword();
        if ($_POST['password'] && $_GET['key'] && changePassword() == true) {
            $success_connection = 'Votre nouveau mot de passe est enregistré avec succès, vous pouvez vous connecter';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'accountform' => accountForm(), 'success_connection' => $success_connection]);
        } else {
            $error_connection = 'Votre mots de passe n\'est pas enregistré';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
        }

    } elseif (get('addComment')) {
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            if (!empty($_GET['id']) && !empty($_POST['comment'])) {
                addComment($_GET['id'], $_SESSION['Auth']['first_name'], $_POST['comment']);
            } else {
                throw new Exception('Tous les champs ne sont pas remplis !');
            }
        } else {
            throw new Exception('Aucun identifiant de billet envoyé');
        }
    } elseif (get('addComment')) {
        $_SESSION = array();
        session_destroy();
        $success_connection = 'Vous êtes déconnecté vous pouvez laisser des commentaires';
        echo $twig->render('home.twig', ['postlist' => listPosts(), 'success_connection' => $success_connection]);
    } elseif (get('contact')) {
        echo $twig->render('contact.twig', ['contactform' => form()]);
    } elseif (get('admin')) {
        if (Auth::adminIsLogged()) {
            $success_connection = 'Vous pouvez ajouter des portfolios et modérer les commentaires';
            echo $twig->render('homeAdmin.twig', ['success_connection' => $success_connection]);
        } else {
            echo $twig->render('homeAdmin.twig', ['adminconnectionform' => adminConnectionForm()]);
        }

    } elseif (get('askAdminConnection')) {
        if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email'])) {
            $error_connection = "Format d'email erroné";
            echo $twig->render('homeAdmin.twig', ['adminconnectionform' => adminConnectionForm(), 'error_connection' => $error_connection]);
        } elseif (!empty($_POST['email']) && !empty($_POST['password']) && askAdminConnection()) {
            $success_connection = 'Vous êtes connecté vous pouvez ajouter des portfolios et modérer les commentaires';
            echo $twig->render('homeAdmin.twig', ['success_connection' => $success_connection]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['adminconnectionform' => adminConnectionForm(), 'error_connection' => $error_connection]);
        }
    } elseif (get('askPostsListAdmin')) {
        if (Auth::adminIsLogged()) {
            echo $twig->render('postsListAdmin.twig', ['postlist' => listPosts()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (get('askCommentsListAdmin')) {
        if (Auth::adminIsLogged()) {
            echo $twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (get('validCom')) {
        if (Auth::adminIsLogged()) {
            validComment();
            echo $twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (get('deleteCom')) {
        if (Auth::adminIsLogged()) {
            deleteComment();
            echo $twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (get('askEditPost')) {
        if (Auth::adminIsLogged() && isset($_GET['id']) && $_GET['id'] > 0) {
            editPostForm($_GET['id']);
            $success_add_post = 'Vous pouvez ajouter un projet';
            echo $twig->render('addSingle.twig', ['editpostform' => editPostForm()]);
        } else {
            $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (get('editPost')) {
        if (Auth::adminIsLogged() && isset($_POST['id']) && $_POST['id'] > 0 && !empty($_POST['title']) && !empty($_POST['content']) && !empty($_POST['realisation_date']) && !empty($_POST['technologies']) && !empty($_POST['url']) && !empty($_POST['intro'])) {
            if (editPost($_POST['id'], $_POST['title'], $_POST['content'], $_POST['realisation_date'], $_POST['technologies'], $_POST['url'], $_POST['intro'])) {
                $success_add_post = 'Le projet est modifié';
                echo $twig->render('addSingle.twig', ['success_add_post' => $success_add_post]);
            }
        } else {
            $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article ou les champs ne sont pas remplis';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (get('askAddPost')) {
        if (Auth::adminIsLogged()) {
            $success_add_post = 'Vous pouvez ajouter un projet';
            echo $twig->render('addSingle.twig', ['addpostform' => postForm()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (get('addPost')) {
        if (!empty($_POST['title']) && !empty($_POST['content']) && !empty($_POST['realisation_date']) && !empty($_POST['technologies']) && !empty($_POST['url']) && !empty($_POST['intro'])) {
            if (addPost($_POST['title'], $_POST['content'], $_POST['realisation_date'], $_POST['technologies'], $_POST['url'], $_POST['intro'])) {
                $success_add_post = 'Le projet est ajouté';
                echo $twig->render('addSingle.twig', ['success_add_post' => $success_add_post]);
            }
        } else {
            throw new Exception('Tous les champs ne sont pas remplis !');
        }
    } else {
        echo $twig->render('home.twig', ['postlist' => listPosts()]);
    }
}
catch(Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}
