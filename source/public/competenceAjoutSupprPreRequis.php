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
$type = "ajout"; //si non précisé, il s'agit d'un ajout de PR

if (isset($_GET['idCible']) && isset($_GET['idPreRequis'])) {
    $idCible = test_input($_GET['idCible']);
    $idPreRequis = test_input($_GET['idPreRequis']);

    if (isset($_GET['type'])) {
        $type = test_input($_GET['type']);
    }


    try {
        $compCible = ObjetMappeFactory::getViaId(Competence::class, $idCible);
        $preRequis = ObjetMappeFactory::getViaId(Competence::class, $idPreRequis);
    } catch (Exception $e) {
        $messageErreur .= "Impossible de trouver la compétence cible ou le pré-requis (Exception levée).<br/>";
        $erreur = true;
        $compCible =null;
        $preRequis = null;
    }
}

if (!empty($compCible) && !empty($preRequis)){

//////// CAS AJOUT
    if ($type == "ajout") {
        if (!$erreur) {
            foreach ($compCible->tablePreRequis as $pr) {
                if ($pr->id__competence == $idPreRequis) {
                    $messageErreur .= "La compétence cible a déjà ce pré-requis.<br/>";
                    $erreur = true;
                }
            }
        }
        if (!$erreur && !$compCible->ajouterPreRequis($preRequis)) {
            $messageErreur .= "Impossible d'ajouter le pré-requis à la compétence cible.<br/>";
            $erreur = true;
        } else {
            $message = "Le pré-requis a bien été ajouté.";
        }
    }
////////FIN CAS AJOUT

    //////// CAS SUPPR
    if ($type == "suppression") {

        if (!$erreur && !$compCible->nePlusAvoirCePreRequis($preRequis)) {
            $messageErreur .= "Impossible de délier le pré-requis de la compétence cible.<br/>";
            $erreur = true;
        } else {
            $message = "Le pré-requis a bien été supprimé.";
        }
    }
////////FIN CAS SUPPR





} else {
    //on ne devrait pas arriver ici
    $idCible = $idPreRequis = 0;
    $erreur = true;
    $messageErreur .= "Requete incorrecte<br/>";
}


?>

<?php if (isset ($message) && !empty($message) && !$erreur) {
    $session->message($message);
    redirigerVers('redirectionMessage.php');
} ?>

<?php include('entete.php'); ?>

<h2>Ajout d'un pré-requis</h2>
<br/>

<?php echo formaterMessage($messageErreur); ?>


<?php include('piedPage.php'); ?>
