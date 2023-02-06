<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
} ?>
<?php

if(empty($_GET['id'])) {
    $session->message("Aucun identifiant de ressource n'a été fourni");
    redirigerVers('index.php');
}
$ress=0;

try {
    $ress = ObjetMappeFactory::getViaId(Ressource::class, test_input($_GET['id']));

    $fichier=$ress->getFichierSansDonnees();



} catch (Exception $e) {
    if (DEBUG) $message = $e->getMessage();
}
if(!$ress) {
    $session->message("Impossible de trouver la ressource demandée.");
    redirigerVers('index.php');
}


?>
<?php include('entete.php'); ?>

<h2>Détails ressource</h2>
<br/>

<?php echo formaterMessage($message); ?>

<?php include('ressourceInclusion.php');?>



<?php include('piedPage.php'); ?>
