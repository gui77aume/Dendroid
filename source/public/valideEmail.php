<?php

require_once("../includes/init.php");
$session = Session::getInstance();
$message = "Un problème est survenu lors de la validation de votre email.";//sera écrasé si OK

if (!empty($_GET['jeton'])) {
//$jeton='093f2d5958539b5fbbee9621ec1b2feb8c478a69156fda78d2af54240c159db382ea7d2642ef50f77559430ee8688991f3ea'; //test
    try {
        $user = ObjetMappeFactory::getViaChamp(Utilisateur::class, 'jeton', $_GET['jeton']);
       //  $user = ObjetMappeFactory::getViaChamp(Utilisateur::class, 'jeton', $jeton); //test

        if ($user) {
            $user->emailVerifie = '1';
//            $user->droits = '5';
            $user->droitsValides = '1';
            $user->sauverDansBase();

            $message = "Bonjour. Merci d'avoir vérifié votre email.<br/> Vous pouvez maintenant vous connecter avec l'identifiant {$user->login}";

        }

    } catch (Exception $e) {
        $message = 'Erreur inattendue.';
    }

}
?>


<?php
$session->message($message);
redirigerVers('redirectionMessage.php');
//include('redirectionMessage.php'); ?>


