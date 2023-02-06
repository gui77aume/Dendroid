<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
} ?>
<?php

if(empty($_GET['id'])) {
    $session->message("Aucun identifiant de compétence n'a été fourni");
    redirigerVers('index.php');
}
$comp=0;
try {
    $comp = ObjetMappeFactory::getViaId(Competence::class, $_GET['id']);
} catch (Exception $e) {
   log_action("Erreur affichage detail compétence",$e->getMessage());
}
if(!$comp) {
    $session->message("Impossible de trouver la compétence demandée.");
    redirigerVers('index.php');
}


?>
<?php include('entete.php'); ?>

<h2>Détails compétence</h2>
<br/>


<?php include('competenceInclusion.php');?>



<?php include('piedPage.php'); ?>
