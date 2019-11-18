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
    if (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'listPosts') {
        echo $twig->render('home.twig', ['postlist' => listPosts()]);
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'post') {
        if (filter_input(INPUT_GET, '', FILTER_SANITIZE_SPECIAL_CHARS) == 'id' > 0 && Auth::isLogged()) {
            echo $twig->render('single.twig', ['post' => post(), 'commentform' => commentForm(), 'commentlist' => listComment()]);
        } elseif (filter_input(INPUT_GET, '', FILTER_SANITIZE_SPECIAL_CHARS) == 'id' > 0) {
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'accountform' => accountForm()]);
        } else {
            throw new Exception('Aucun identifiant de portfolio envoyé');
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'addAccount') {
        if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS))) {
            $error_inscription = "Format d'email erroné";
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'accountform' => accountForm(), 'error_inscription' => $error_inscription]);
        } elseif (!empty(filter_input(INPUT_POST, 'first_name_account', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS) && !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS)))) {
            $manager = new Manager();
            $bdd = $manager->dbConnect();
            $stmt = $bdd->prepare('SELECT * FROM member WHERE email=?');
            $stmt->bindValue(1, filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS));
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $error_inscription = "Format d'email erroné";
                echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'accountform' => accountForm(), 'error_inscription' => $error_inscription]);
            } else {
                addAccount(filter_input(INPUT_POST, 'first_name_account', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
                $success_inscription = 'Votre compte est créé vous pouvez laisser des commentaires';
                echo $twig->render('single.twig', ['post' => post(), 'commentform' => commentForm(), 'commentlist' => listComment(), 'success_inscription' => $success_inscription]);
            }
        } else {
            throw new Exception('Tous les champs ne sont pas remplis !');
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'askConnection') {
        if (!empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) and !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))){
            if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS))) {
                $error_connection = "Format d'email erroné";
                echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'member' => askConnection(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
            } else {
                askConnection();
                if ((!empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS)) && askConnection()) or Auth::adminIsLogged()) {
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
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'forgetPassword') {
        echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm()]);
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'askNewPassword') {
        askNewPassword();
        if (!empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) && askNewPassword() == true) {
            $success_connection = 'Vous allez recevoir un email pour réinitialiser votre mot de passe';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'success_connection' => $success_connection]);
        } else {
            $error_connection = 'Votre e-mail n\'est pas reconnu';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'resetPassword') {
        checkIfPasswordKeyExist();
        if (filter_input(INPUT_GET, '', FILTER_SANITIZE_SPECIAL_CHARS) == 'key' && checkIfPasswordKeyExist() == true) {
            $success_connection = 'Renseignez votre nouveau mot de passe';
            echo $twig->render('single.twig', ['post' => post(), 'newpasswordform' => newPasswordForm(), 'key' => get('','key'), 'success_connection' => $success_connection]);
        } else {
            $error_connection = 'Votre lien n\'est pas reconnu ou n\'est plus valide';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'resetPassword') {
        changePassword();
        if (filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) && filter_input(INPUT_GET, '', FILTER_SANITIZE_SPECIAL_CHARS) == 'key' && changePassword() == true) {
            $success_connection = 'Votre nouveau mot de passe est enregistré avec succès, vous pouvez vous connecter';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'accountform' => accountForm(), 'success_connection' => $success_connection]);
        } else {
            $error_connection = 'Votre mots de passe n\'est pas enregistré';
            echo $twig->render('single.twig', ['post' => post(), 'connectionform' => connectionForm(), 'commentlist' => listComment(), 'resetpasswordform' => askResetingPassword(), 'accountform' => accountForm(), 'error_connection' => $error_connection]);
        }

    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'addComment') {
        if (filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS) > 0) {
            if (!empty(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS))) {
                addComment(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS), $_SESSION['Auth']['first_name'], filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS));
            } else {
                throw new Exception('Tous les champs ne sont pas remplis !');
            }
        } else {
            throw new Exception('Aucun identifiant de billet envoyé');
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'addComment') {
        $_SESSION = array();
        session_destroy();
        $success_connection = 'Vous êtes déconnecté vous pouvez laisser des commentaires';
        echo $twig->render('home.twig', ['postlist' => listPosts(), 'success_connection' => $success_connection]);
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'contact') {
        echo $twig->render('contact.twig', ['contactform' => form()]);
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'admin') {
        if (Auth::adminIsLogged()) {
            $success_connection = 'Vous pouvez ajouter des portfolios et modérer les commentaires';
            echo $twig->render('homeAdmin.twig', ['success_connection' => $success_connection]);
        } else {
            echo $twig->render('homeAdmin.twig', ['adminconnectionform' => adminConnectionForm()]);
        }

    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'askAdminConnection') {
        if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS))) {
            $error_connection = "Format d'email erroné";
            echo $twig->render('homeAdmin.twig', ['adminconnectionform' => adminConnectionForm(), 'error_connection' => $error_connection]);
        } elseif (!empty(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS)) && askAdminConnection()) {
            $success_connection = 'Vous êtes connecté vous pouvez ajouter des portfolios et modérer les commentaires';
            echo $twig->render('homeAdmin.twig', ['success_connection' => $success_connection]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['adminconnectionform' => adminConnectionForm(), 'error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'askPostsListAdmin') {
        if (Auth::adminIsLogged()) {
            echo $twig->render('postsListAdmin.twig', ['postlist' => listPosts()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'askCommentsListAdmin') {
        if (Auth::adminIsLogged()) {
            echo $twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'validCom') {
        if (Auth::adminIsLogged()) {
            validComment();
            echo $twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'deleteCom') {
        if (Auth::adminIsLogged()) {
            deleteComment();
            echo $twig->render('commentsListAdmin.twig', ['commentlist' => listNewComments()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'askEditPost') {
        if (Auth::adminIsLogged() && filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS) > 0) {
            editPostForm(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS));
            $success_add_post = 'Vous pouvez ajouter un projet';
            echo $twig->render('addSingle.twig', ['editpostform' => editPostForm()]);
        } else {
            $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'editPost') {
        if (Auth::adminIsLogged() && !empty(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS)) && filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS) > 0 && !empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
            if (editPost(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
                $success_add_post = 'Le projet est modifié';
                echo $twig->render('addSingle.twig', ['success_add_post' => $success_add_post]);
            }
        } else {
            $error_connection = 'Vous n\'êtes pas autorisé à modifier cet article ou les champs ne sont pas remplis';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif (filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'askAddPost') {
        if (Auth::adminIsLogged()) {
            $success_add_post = 'Vous pouvez ajouter un projet';
            echo $twig->render('addSingle.twig', ['addpostform' => postForm()]);
        } else {
            $error_connection = 'Mauvais identifiant ou mot de passe !';
            echo $twig->render('homeAdmin.twig', ['error_connection' => $error_connection]);
        }
    } elseif ( filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) == 'addPost') {
        if (!empty(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS)) && !empty(filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
            if (filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'content', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'realisation_date', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'technologies', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'intro', FILTER_SANITIZE_SPECIAL_CHARS))) {
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
