<?php
require_once("../includes/init.php");

if(isset($_GET['login'])){

    if (!preg_match("/^[a-z0-9]*$/", $_GET['login'])) {
        echo "false";
        exit();
    }

    $login=test_input($_GET['login']);

    if (empty($login)) {
        echo "false";
        exit();
    }

    //Existence de l'identifiant ?
    try {
        $user = ObjetMappeFactory::getViaChamp(Utilisateur::class, 'login', $login);
        if (!empty($user)) {
            echo "false";
            exit();
        }
    } catch (Exception $e) {
    }
echo "true";
}
else {
    echo "false";
}


