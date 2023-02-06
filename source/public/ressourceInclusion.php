<!--Fichier à inclure pour afficher une ressource-->
<!--$ress et $fichier doivent être initialisés avant inclusion -->
<?php
/**
 * @var Fichier $fichier
 * @var Ressource $ress
 */
?>

<?php require_once("../includes/init.php"); ?>
<?php
if (isset($idCompCible) && $idCompCible != 0) { //on affiche une ressource potentiellement bientôt liée à CompCible
    $liens = "<div><a href=\"ressourceLierDelierCompetence.php?idCompetence={$idCompCible}&idRessource={$ress->id}\">Ajouter cette ressource</a></div>";
} else {
    $liens = "<div><a href=\"ressourceAjoutModif.php?id={$ress->id}\">Modifier</a> &nbsp; <a href=\"ressourceSuppression.php?id={$ress->id}\">Supprimer</a>";
    $liens .= " &nbsp; <a href=\"competencesListe.php?idRessCible={$ress->id}\">Lier à une compétence</a></div>";
}
$affichageFichier ="";
if ($fichier) {
    $affichageFichier = "<strong>Fichier associé :</strong> &nbsp;&nbsp;&nbsp;   <a href = \"fichierTelechargement.php?id=" . $fichier->id . "\">" . $fichier->nom . "</a> <br><br>";
}

$comps="";
$compResArray = Concerner::getCompetencesAssociees($ress->id);
if(!empty($compResArray)) {
    $comps="Compétence(s) utilisant cette ressource : <br/><br/>";
    foreach ($compResArray as $comp)
        $comps .= "<div><a href=\"competenceDetail.php?id=$comp->id\">$comp->nom</a></div><br>";
}
?>

<div class="entiteCadree clearfix">
    <h3><?php echo $ress->titre ?></h3>
    <div class="hautEntite">



        <?php if(!empty($ress->auteur)) echo "<div>Auteur :  $ress->auteur </div> <br/><br/>"; ?>

        <?php if(!empty($ress->URL)) echo "<div>URL:  <a target=\"_blank\" href=\"$ress->URL\">$ress->URL</a> </div> <br/> <br/>"; ?>

        <?php echo $affichageFichier; ?>

        <?php if(!empty($ress->description)) echo "<div>Description : <br> $ress->description </div> <br/><br/>"; ?>

        <?php if(!empty($ress->motsClefs)) echo "<div>Mots clefs :  <br> $ress->motsClefs </div> <br/><br/>"; ?>

        <?php echo $comps ?>

    </div>

    <div class="basEntite">
        <?php echo $liens ?>
    </div>
</div>

