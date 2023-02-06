<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("../connexion.php");
} ?>
<?php

/**
 * Page utilisée pour la création ou la modification d'une compétence
 */


$message = "";
$erreur = false;


if (isset($_GET['id']))  //Mode édition
    $id = test_input($_GET['id']);
else
    $id = 0; //Mode ajout


if (isset($_GET['submit'])) { //On sauvegarde

//Vérif format
    $nom = test_input($_GET['nom']);
    $nomCourt = test_input($_GET['nomCourt']);
    $description = test_input($_GET['description']);
//champs complétés ?
    if (empty($_GET["nom"]) || empty($_GET['nomCourt'])) {
        $erreur = true;
        $message .= "Merci de saisir un nom <u>et</u> un nom abrégé.<br/>";
    }

    //todo proposer une chaine compatible
    if (!preg_match("/^[a-zA-Z0-9_]+$/", $nomCourt)) {
        $message .= "Merci de ne pas utiliser de caractères spéciaux pour le nom court (remplacer les espacements par \"_\")<br/>";
        $erreur = true;
    }

    if (strlen($nomCourt) > 30) {
        $message .= "Merci de ne pas utiliser plus de 30 caractères  pour le nom court.<br/>";
        $erreur = true;
    }

    //Si création : test existence de la compétence
    if ($id == 0) {
        try {
            $compTest = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', $nom);
            if (!empty($compTest)) {
                $message .= "Ce nom est déjà utilisé, merci d'en choisir un nouveau.<br/>";
                $erreur = true;
            }
        } catch (Exception $ignored) {
        }

        try {
            $compTest = ObjetMappeFactory::getViaChamp(Competence::class, 'nomCourt', $nomCourt);
            if (!empty($compTest)) {
                $message .= "Ce nom abrégé est déjà utilisé, merci d'en choisir un nouveau.<br/>";
                $erreur = true;
            }
        } catch (Exception $ignored) {
        }

    }

    //Création de l'objet comp vide ou chargement si existe dans la base
    if (!$erreur) {
        if ($id == 0) {//création d'une nouvelle compétence
            $compAEnregistrer = new Competence();
        } else { //modification
            try {
                $compAEnregistrer = ObjetMappeFactory::getViaId(Competence::class, $id);
                $changement = ($compAEnregistrer->nom != $nom || $compAEnregistrer->nomCourt != $nomCourt || $compAEnregistrer->description != $description);
                if (!$changement) {
                    $message .= "Pas de changement à apporter à cette compétence.<br/>";
                    $erreur = true;
                }
            } catch (Exception $e) {
                $compAEnregistrer=null;
                $message .= "Impossible de sauvegarder la compétence : erreur au chargement des donnees de l'objet existant.<br/>";
                $erreur = true;
            }

        }

    }

    //il y a une nouvelle comp ou un changement à sauvegarger
    if (!$erreur) {
        $compAEnregistrer->nom = $nom;
        $compAEnregistrer->nomCourt = $nomCourt;
        $compAEnregistrer->description = $description;
        if ($compAEnregistrer->sauverDansBase()) {
            $message .= "La compétence a été enregistrée.<br/>";
            $session->message($message);
            redirigerVers('redirectionMessage.php');
        } else {
            $message .= "La sauvegarde de la compétence n'a pas affecté la base de données (?).<br/>";
            $erreur = true;
        }


    }
}


//Affichage
if ($id != 0) { // on charge la comp
    $titrePage = "Modification d'une compétence";
    $lienAjoutPreRequis = "<div class=\"hautEntite\" id=\"ajouter\" ><a href=\"competencesListe.php?idCompCible={$id}\">Ajouter un pré-requis</a></div>";

    try {
        $compChargee = ObjetMappeFactory::getViaId(Competence::class, $id);

        if (empty($compChargee)) {
            $message .= "Impossible de trouver la compétence demandée.<br/>";
            $erreur = true;
        } else {
            $nom = $compChargee->nom;
            $nomCourt = $compChargee->nomCourt;
            $description = $compChargee->description;
        }
    } catch (Exception $e) {
        $message .= "Erreur lors du chargement la compétence demandée.<br/>";
        $erreur = true;
    }


} else { //affichage page neuve pour une création de compétence

    if (isset($_GET['submit'])) { //On reprend qd meme les params pour ne pas devoir ressaisir
        $nom = test_input($_GET['nom']);
        $nomCourt = test_input($_GET['nomCourt']);
        $description = test_input($_GET['description']);
    } else {
        $nom = "";
        $nomCourt = "";
        $description = "";
    }

    $titrePage = "Ajout d'une compétence";
    $lienAjoutPreRequis = "";
}

?>

<?php


//if ($erreur) {
//    redirigerVers("competenceAjoutModif.php?id=" . $id);
//} else {
//    redirigerVers('index.php');
//    // redirigerVers("competenceAjoutModif.php?id=".$id);
//}

?>

<?php include('entete.php'); ?>

<script>
    function modif(){
        const divlien = document.getElementById("ajouter");
        console.log("modif");
        divlien.innerHTML="Les modifications doivent être enregistréee avant d'ajouter un pré-requis "
    }
</script>

<h2><?php echo $titrePage ?></h2>
<br/>

<?php //echo formaterMessage($message); ?>
<?php echo formaterMessage($message); ?>

<div class="entiteCadree clearfix">
    <form action="competenceAjoutModif.php" method="get">
        <h3><label>Nom : <input type="text" name="nom" value="<?php echo $nom ?>" onchange="modif()" onkeyup="modif()"/></label></h3>
        <div class="hautEntite">
            <label>Nom court: <input type="text" name="nomCourt" value="<?php echo $nomCourt ?>" onchange="modif()" onkeyup="modif()" /></label>
            <br/>
            <br/>
            <label>Description : <textarea name="description" rows="5" cols="70" onchange="modif()" onkeyup="modif()"><?php echo $description ?></textarea>
            </label>
        </div>

        <input type="hidden" name="id" value="<?php echo $id ?>">

        <?php if ($compChargee->tablePreRequis) echo '<div class="hautEntite"> <p>Liste des pré-requis :</p>' ?>

        <?php foreach ($compChargee->tablePreRequis as $preRequis):
            try {
                $compRequise = ObjetMappeFactory::getViaId(Competence::class, $preRequis->id__competence);
            } catch (Exception $e) {
                $compRequise = new Competence(); //TODO gérer erreur mieux que ça
            }
            ?>
            <div style="display: table-row">
                <div style="display:table-cell">
                    <a href="competenceDetail.php?id=<?php echo $compRequise->id ?>"><?php echo $compRequise->nom; ?><?php ?></a>
                </div>
                <div style="display:table-cell">
                    <a style="display: table-cell"
                       href="competenceAjoutSupprPreRequis.php?idCible=<?php echo $id ?>&idPreRequis=<?php echo $compRequise->id ?>&type=suppression">Supprimer
                        ce pré-requis</a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if ($compChargee->tablePreRequis) echo '</div>' ?>

        <?php echo $lienAjoutPreRequis ?>

        <div class="basEntite">
            <div><input type="submit" name="submit" value="Enregistrer" class="boutonVert"/></div>
        </div>

    </form>

</div>


<?php include('piedPage.php'); ?>
