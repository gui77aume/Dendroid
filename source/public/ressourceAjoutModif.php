<?php

require_once("../includes/init.php");
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
}


$message = "";
$erreur = false;


//Mode édition ou mode ajout ?
if (isset($_REQUEST['id']))  //Mode édition
{
    $id = test_input($_REQUEST['id']); //on veut pouvoir spécifier l'id pas les deux méthodes
} else {
    $id = 0; //Mode ajout
}

//un fichier associé existe-t-il ?
try {
    $fichier = ObjetMappeFactory::getViaChamp(Fichier::class, 'id__ressource', $id);
} catch (Exception $e) {
    $fichier = null;
}

//Submit = On sauvegarde
if (isset($_POST['submit'])) {

//Vérif format

    $titre = test_input($_POST['titre']);
    $description = test_input($_POST['description']);
    $auteur = test_input($_POST['auteur']);
    $conversionTexte = "_"; //test_input($_POST['conversionTexte']); TODO: automatiser depuis fichier
    $motsClefs = test_input($_POST['motsClefs']);
    $URL = test_input($_POST['URL']);


    //champs complétés ?
    if (empty($_POST["titre"])) {  //|| empty($_POST['publique'])) {
        $erreur = true;
        $message .= "Merci de saisir un titre. <br/><br/>";
    }

    if (!empty($URL) && !(contient('http://', $URL) || contient('https://', $URL))) {
        $message .= "L'URL fournie ne commence pas par http:// ni par https://.<br>Si cette URL est celle d'un site web, merci d'ajouter le préfixe adhoc.<br/><br/>";
    }


    //Si création : test existence de la ressource
    if ($id == 0) {
        try {
            $ressTest = ObjetMappeFactory::getViaChamp(Ressource::class, 'titre', $titre);
            if (!empty($ressTest)) {
                $message .= "Ce titre est déjà utilisé, merci d'en choisir un nouveau.<br/><br/>";
                $erreur = true;
            }
        } catch (Exception $ignored) {
        }
    }

    //Création de l'objet ress vide ou chargement si existe dans la base
    if (!$erreur) {
        if ($id == 0) {
            //création d'une nouvelle ressource
            $ressAEnregistrer = new Ressource();
        } else {
            //modification
            try {
                $ressAEnregistrer = ObjetMappeFactory::getViaId(Ressource::class, $id);
            } catch (Exception $e) {
                $message .= "Impossible de sauvegarder la ressource : erreur au chargement des donnees de l'objet existant.<br/><br/>";
                log_action("Impossible de sauvegarder la ressource : erreur au chargement des donnees de l'objet existant.", $e->getMessage());
                $erreur = true;
            }
        }

    }


    //il y a une nouvelle ress ou un changement à sauvegarger et TVB
    if (!$erreur) {

        $ressAEnregistrer->titre = $titre;
        $ressAEnregistrer->description = $description;
        $ressAEnregistrer->auteur = $auteur;
        $ressAEnregistrer->motsClefs = $motsClefs;
        $ressAEnregistrer->URL = $URL;

        //Dates créa/maj
        if ($id == 0) {
            $ressAEnregistrer->dateAjout = date('Y-m-d H:i:s');
            $ressAEnregistrer->dateMAJ = date('Y-m-d H:i:s'); //TODO gérer mieux la date maj. Vide initialement ?
        } else {
            $ressAEnregistrer->dateMAJ = date('Y-m-d H:i:s');
        }

        $ressAEnregistrer->conversionTexte = "_"; //todo supprimer ces lignes si implémentation
        $ressAEnregistrer->validationQualite = "0";
        $ressAEnregistrer->obsolete = "0";
        $ressAEnregistrer->publique = "0";
        $ressAEnregistrer->refQualite = "_";

        //sauvegarde ressource
        if ($ressAEnregistrer->sauverDansBase() == 1) {
            $message .= "La ressource a été enregistrée.<br/><br/>";//<a href=\"index.php\">Retour à l'accueil</a> <br>";
            $id = $ressAEnregistrer->id;
        } else {
            $message .= "L'enregistrement de la ressource n'a pas affecté la base de données.<br/><br/>";
        }
    }

    //////////
    /// Enregistrement du fichier
    //on enregistre que si la ressource a bien été crée
    if ($id != 0) {

        if (isset($_FILES['file']['name'])) {//&& isset($_FILES['file']['tmp_name']) && isset($_FILES['file']['size']) && isset($_FILES['file']['type'])) {

            $filename = test_input($_FILES['file']['name']);
            $tmpname = $_FILES['file']['tmp_name'];
            $file_size = $_FILES['file']['size'];
            $file_type = $_FILES['file']['type'];
            $content = file_get_contents($tmpname);


            if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {

                if ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
//                    $message .= "Information : pas de fichier associé à modifier.";
                } else {
                    $message .= "Erreur lors de l'envoi du fichier : " . Fichier::traductionErreurs($_FILES['file']['error']) . '<br/><br/>';
                    log_action('Erreur lors de l\'envoi d\'un fichier', $message);
                }
            } else {


                //on efface le fichier s'il existe
                try {
                    ObjetMappeFactory::getViaDeuxChamps(Fichier::class, "nom", $filename, "id__ressource", $id)->effacerDeBase();


                } catch
                (Exception $e) {
                }

                if (isset($ressAEnregistrer) && !empty($ressAEnregistrer)) {
                    $f = $ressAEnregistrer->getFichier();
                    if (!empty($f)) {
                        $f->effacerDeBase();
                    }
                }

                $fichier = new Fichier();
                $fichier->nom = $filename;
                $fichier->type = $file_type;
                $fichier->taille = $file_size;
                $fichier->donnees = $content; //BaseDonnees::getInstance()->echapper($content);// addslashes($content);
                $fichier->id__ressource = $id;

                if ($fichier->sauverDansBase() == 1) {
                    $message .= 'Le fichier associé a bien été envoyé.<br/>Merci de bien vouloir vérifier son intégrité en le téléchargeant.<br/><br/>';

                } else {
                    $message .= 'L\'envoi du fichier associé n\'a pas affecté la base de données (fichier identique à l\'existant ?).<br/><br/>';
                }
            }
        }
    }

}


//Préparation de l'affichage

$titrePage = "Ajout d'une ressource";
$affichageFichier = "Pas de fichier associé.<br>";//
$affichageFichier .= '<div>Ajouter un fichier :<input type="file" name="file" style="width: auto"/></div>';


// on charge la ressource ou on affiche une page vide
if ($id != 0) {
    //on écrase le titre
    $titrePage = "Modification d'une ressource";

    if ($fichier != null) {
        //on écrase $affichageFicher
        $affichageFichier = "<strong>Fichier associé :</strong> &nbsp;&nbsp;&nbsp;   <a href = \"fichierTelechargement.php?id=" . $fichier->id . "\">" . $fichier->nom . "</a> <br>";
        $affichageFichier .= '<div>Remplacer le fichier :<input type="file" name="file" style="width: auto"/></div>';
    }


    try {
        $ressChargee = ObjetMappeFactory::getViaId(Ressource::class, $id);

        if (empty($ressChargee)) {
            $message .= "Impossible de trouver la ressource demandée.<br/>";
            log_action("Impossible de trouver la ressource demandée. n° $id", "ObjetMappeFactory::getViaId(Ressource::class, $id); ne renvoie rien.");
            $erreur = true;
        } else {

            $titre = $ressChargee->titre;
            $description = $ressChargee->description;
            $auteur = $ressChargee->auteur;
            $conversionTexte = $ressChargee->conversionTexte; //TODO: automatiser depuis fichier
            $dateAjout = $ressChargee->dateAjout; //TODO : gérer dans sauverDansBase etc.
            $dateMAJ = $ressChargee->dateMAJ; //TODO : gérer dans sauverDansBase etc.
            $motsClefs = $ressChargee->motsClefs;
            $obsolete = $ressChargee->obsolete;
            $publique = $ressChargee->publique;
            $refQualite = $ressChargee->refQualite;
            $URL = $ressChargee->URL;
            $validationQualite = $ressChargee->validationQualite;


        }
    } catch (Exception $e) {
        log_action("Exception au chargement de la ressource ressource", $e->getMessage());
        $message .= "Erreur lors du chargement la ressource demandée.<br/>";
        $erreur = true;
    }

//affichage page neuve pour une création de ressource
} else {

    if (isset($_POST['submit'])) { //On reprend qd meme les params pour ne pas devoir ressaisir
        $titre = test_input($_POST['titre']);
        $description = test_input($_POST['description']);
        $auteur = test_input($_POST['auteur']);
        $conversionTexte = ""; //test_input($_POST['conversionTexte']); TODO: automatiser depuis fichier
        //$dateAjout =test_input($_POST['']); //TODO : gérer dans sauverDansBase etc.
        //$dateMAJ = test_input($_POST['']); //TODO : gérer dans sauverDansBase etc.
        $motsClefs = test_input($_POST['motsClefs']);
        $obsolete = "0"; //test_input($_POST['obsolete']);
        $publique = "0"; // test_input($_POST['publique']);
        $refQualite = ""; //test_input($_POST['refQualite']);
        $URL = test_input($_POST['URL']);
        $validationQualite = "0"; // test_input($_POST['validationQualite']);
    } else {
        $titre = "";
        $description = "";
        $auteur = "";
        $conversionTexte = ""; //test_input($_POST['conversionTexte']); TODO: automatiser depuis fichier
        //$dateAjout =test_input($_POST['']); //TODO : gérer dans sauverDansBase etc.
        //$dateMAJ = test_input($_POST['']); //TODO : gérer dans sauverDansBase etc.
        $motsClefs = "";
        $obsolete = "0";
        $publique = "0";
        $refQualite = "";
        $URL = "";
        $validationQualite = "0";
    }


}


?>




<?php //La vue commence ici !
include('entete.php'); ?>

<h2><?php echo $titrePage ?></h2>
<br/>

<?php echo formaterMessage($message); ?>

<div class="entiteCadree clearfix">
    <form action="ressourceAjoutModif.php" method="post" enctype="multipart/form-data">
        <h3><label>Titre : <input type="text" name="titre" value="<?php echo $titre ?>"/></label></h3>
        <div class="hautEntite">

            Auteur :<br>
            <div><input type="text" name="auteur" value="<?php echo $auteur ?>"/></div>
            <br/>


            URL :<br>
            <div><input type="text" name="URL" value="<?php echo $URL ?>"/></div>
            <br/>


            <br>
            <hr>
            <?php echo $affichageFichier; ?>
            <hr>
            <br>

            Description :
            <div><textarea name="description" rows="5" cols="70"><?php echo $description ?></textarea></div>

            Mots clefs :
            <div><textarea name="motsClefs" rows="5" cols="70"><?php echo $motsClefs ?></textarea></div>

            <br>


            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_ENVOI_FICHIER_FORM ?>"/>


            <div class="basEntite">
                <div><input type="submit" name="submit" value="Enregistrer" class="boutonVert"/></div>
            </div>

    </form>

</div>


<?php include('piedPage.php'); ?>
