<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
} ?>
<?php

/**
 * Page muette si tout va bien
 * Permet d'ajouter le pré-requis à la compétence cible dont les id sont passée via GET
 */

$messageErreur = "";
$erreur = false; //permet d'arreter la chaine de vérifs


if (isset($_GET['idRessource']) && isset($_GET['idCompetence'])) {
    $idRessource = test_input($_GET['idRessource']);
    $idCompetence = test_input($_GET['idCompetence']);

    if (isset($_GET['type'])) {
        $type = test_input($_GET['type']);
    } else $type = "creation"; //si non précisé, il s'agit d'un ajout de PR

//if(true){ //test
//    $idCible = 266; //test
//    $idPreRequis = 281; //test

    try {
        $ressource = ObjetMappeFactory::getViaId(Ressource::class, $idRessource);
        $competence = ObjetMappeFactory::getViaId(Competence::class, $idCompetence);

    } catch (Exception $e) {
        $messageErreur .= "Impossible de trouver la compétence ou la ressource.<br/>";
        $erreur = true;
    }

if(!$erreur && !empty($ressource) && !empty($competence)) {
//////// CAS creation lien
    if ($type == "creation") {

        if (Concerner::lierCompetenceARessource($competence->id, $ressource->id) != 1) {
            $messageErreur .= "La liaison ressource-compétence n'a pas affecté la base.<br/>";
            $erreur = true;
        } else {
            $message = "La liaison ressource-compétence a été effectuée.";
        }
    }
////////FIN CAS AJOUT

    //////// CAS SUPPR
    if ($type == "suppression") {

        if (Concerner::delierCompetenceDeRessource($competence->id, $ressource->id) != 1) {
            $messageErreur .= "Impossible de supprimer la liaison.<br/>";
            $erreur = true;
        } else {
            $message = "Le lien ressource-compétence a bien été supprimé.";
        }
    }
////////FIN CAS SUPPR
}




} else {
    //on ne devrait pas arriver ici
    $idCompetence = $idRessource = 0;
    $erreur = true;
    $messageErreur .= "Requete incorrecte<br/>";
}


?>

<?php if (isset ($message) && !empty($message) && !$erreur) {
    $session->message($message);
   // redirigerVers('index.php');
    redirigerVers('redirectionMessage.php');

//    include('redirectionMessage.php');
//    exit();
} ?>

<?php include('entete.php'); ?>

<h2>Lier ou délier une compétence et une ressource</h2>
<br/>

<?php echo formaterMessage($messageErreur); ?>


<?php include('piedPage.php'); ?>
