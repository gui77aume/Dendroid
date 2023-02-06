
<?php

//TODO factoriser avec detailRessource - ressourceInclusion etc.

require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
} ?>
<?php

$message="";
if (empty($_GET['id'])) {
    $session->message("Aucun identifiant de ressource n'a été fourni");
    redirigerVers('index.php');
}
try {
    $idOk = test_input($_GET['id']);
    $ress = ObjetMappeFactory::getViaId(Ressource::class, $idOk);

    $fichier = $ress->getFichier();

    $affichageFichier ="";
    if ($fichier) {
        $affichageFichier = "<strong>Fichier associé :</strong> &nbsp;&nbsp;&nbsp;   <a href = \"fichierTelechargement.php?id=" . $fichier->id . "\">" . $fichier->nom . "</a> <br>";
    }


} catch (Exception $e) {
    $ress = 0;
     log_action("Erreur à la suppression de la ressource", $e->getMessage());
}
if (!$ress) {
    $session->message("Impossible de trouver la ressource demandée.");
    redirigerVers('index.php');
}

$effacer = test_input($_GET['confirmation']);
if (!empty($effacer) && !strcmp($effacer, hash("md5",$ress->id))) { //md5 = sécurité basique pour ne pas effacer involontairement
    if($ress->effacerDeBase()==1) {
        $session->message("Ressource supprimée .");
    }
    else {
        $session->message("La suppression n'a pas affecté la base de données.");
    }

    redirigerVers('redirectionMessage.php');
}

?>
<?php include('entete.php'); ?>

<?php echo formaterMessage($message); ?>

<h2>Supprimer cette ressource de la base ?</h2>
<h3>Les éventuels fichiers associés seront supprimés</h3>
<br/>

<div class="entiteCadree clearfix">
    <h3><?php echo $ress->titre ?></h3>
    <div class="hautEntite">


        <?php if(!empty($ress->auteur)) echo "<div>Auteur :  $ress->auteur </div> <br/>"; ?>
        <br>
        <?php if(!empty($ress->URL)) echo "<div>URL:  <a target=\"_blank\" href=\"$ress->URL\">$ress->URL</a> </div> <br/>"; ?>
        <br>
        <?php echo $affichageFichier; ?>
        <br>
        <?php if(!empty($ress->description)) echo "<div>Description : <br> $ress->description </div> <br/>"; ?>
        <br>
        <?php if(!empty($ress->motsClefs)) echo "<div>Mots clefs :  <br> $ress->motsClefs </div> <br/>"; ?>

    </div>



    <div class="basEntite">
        <div>
            <a href="ressourceSuppression.php?id=<?php echo $ress->id ?>&confirmation=<?php echo hash("md5",$ress->id) ?>">Supprimer
                définitivement</a> &nbsp;
        </div>
    </div>

</div>


<?php include('piedPage.php'); ?>