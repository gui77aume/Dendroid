<!--Fichier à inclure pour afficher une compétence-->
<!--$comp doit être initialisée avant inclusion -->

<?php
/**
 * @var Competence $comp
 */
?>

<?php require_once("../includes/init.php"); ?>
<?php
if (isset($idCompCible) && $idCompCible != 0) { //on affiche un pré-requis potentiel de CompCible
    $liens = "<div><a href=\"competenceAjoutSupprPreRequis.php?idCible={$idCompCible}&idPreRequis={$comp->id}\">Ajouter ce pré-requis</a></div>";
} else {
    if (isset($idRessCible) && $idRessCible != 0) { //on affiche une compétence à lier à  RessCible
        $liens = "<div><a href=\"ressourceLierDelierCompetence.php?idCompetence={$comp->id}&idRessource={$idRessCible}\">Lier cette compétence à la ressource</a></div>";
    } else {

        $liens = "<div><a href=\"competenceAjoutModif.php?id={$comp->id}\">Modifier</a>";
        $liens .= " &nbsp; <a href=\"competenceSuppression.php?id={$comp->id}\">Supprimer</a>";
        $liens .= " &nbsp; <a href=\"ressourcesListe.php?idCompCible={$comp->id}\">Lier à une ressource</a></div>";
    }
}

$ressources = "";
$ResArray = Concerner::getRessourcesAssociees($comp->id);
if (!empty($ResArray)) {
    $ressources = "<div class=\"hautEntite\"> Ressource(s) associée(s) à cette compétence : <br/><br/>";
    foreach ($ResArray as $res) {
        $ressources .= "<div><a href=\"ressourceDetail.php?id=$res->id\">$res->titre</a></div><br>";
    }
    $ressources .= '</div>';
}

?>

<div class="entiteCadree clearfix">
    <h3><?php echo $comp->nom ?></h3>
    <div class="hautEntite">
        <div>Description : <?php echo $comp->description; ?></div>
        <br/>
        <?php if (!empty($comp->nomCourt)) echo "<div>Nom abrégé :  $comp->nomCourt </div>"; ?>

    </div>
    <?php if ($comp->tablePreRequis) echo '<div class="hautEntite"> <p>Liste des pré-requis :</p><br/>' ?>

    <?php foreach ($comp->tablePreRequis as $preRequis):
        try {
            $compRequise = ObjetMappeFactory::getViaId(Competence::class, $preRequis->id__competence);
        } catch (Exception $e) {
            $compRequise = new Competence(); //TODO gérer erreur mieux que ça : à déplacer dans le controleur...
        }
        ?>
        <div>
            <a href="competenceDetail.php?id=<?php echo $compRequise->id ?>"><?php echo $compRequise->nom; ?><?php ?></a>
        </div>
        <br/>
    <?php endforeach; ?>

    <?php

    if ($comp->tablePreRequis) echo '</div>';

    echo $ressources;
    ?>


    <div class="basEntite">
        <?php echo $liens ?>
    </div>
</div>

