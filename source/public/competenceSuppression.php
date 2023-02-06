<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
} ?>
<?php

$message="";
if (empty($_GET['id'])) {
    $session->message("Aucun identifiant de compétence n'a été fourni");
    redirigerVers('index.php');
}
try {
    $idOk = test_input($_GET['id']);
    $comp = ObjetMappeFactory::getViaId(Competence::class, $idOk);
} catch (Exception $e) {
    if (DEBUG) $message = $e->getMessage();
    $session->message("Impossible de trouver la compétence demandée.");
    redirigerVers('index.php');
    $comp=null; //pour mess erreur check

}


if(empty($comp)){
    $session->message("Impossible de trouver la compétence demandée.");
    redirigerVers('index.php');
}

$effacer = test_input($_GET['confirmation']);
if (!empty($effacer) && !strcmp($effacer, hash("md5",$comp->id))) { //md5 = sécurité basique pour ne pas effacer involontairement
    if($comp->effacerDeBase())  $session->message("Compétence supprimée ."); else  $session->message("Erreur lors de la suppression ?.");
    redirigerVers('redirectionMessage.php');
}

?>
<?php include('entete.php'); ?>

<?php echo formaterMessage($message); ?>

<h2>Supprimer cette compétence de la base ?</h2>
<h3>Les liens avec les autres compétences seront supprimés</h3>
<br/>

<div class="entiteCadree clearfix">
    <h3><?php echo $comp->nom ?></h3>
    <div class="hautEntite">
        <div><?php echo $comp->description; ?></div>
    </div>
    <?php if ($comp->tablePreRequis) echo '<div class="hautEntite"> <p>Liste des pré-requis :</p>' ?>

    <?php foreach ($comp->tablePreRequis as $preRequis):
        try {
            $compRequise = ObjetMappeFactory::getViaId(Competence::class, $preRequis->id__competence);
             echo "<div><a href=\"<?php RACINE ?>index.php\"><?php echo $compRequise->nom; ?><?php ?></a></div>";
        } catch (Exception $e) {
        }
        ?>

    <?php endforeach; ?>
    <?php if ($comp->tablePreRequis) echo '</div>' ?>

    <div class="basEntite">
        <div>
            <a href="competenceSuppression.php?id=<?php echo $comp->id ?>&confirmation=<?php echo hash("md5",$comp->id) ?>">Supprimer
                définitivement</a> &nbsp;
        </div>
    </div>

</div>


<?php include('piedPage.php'); ?>