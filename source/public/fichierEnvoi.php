<?php

require_once("../includes/init.php");
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
}

$message = "";
$idRessource=-1;

//l'id de la ressoource associée peut être passsée par get depuis page source ou par post si via formulaire de cette page
if (isset($_GET['idRessource'])) {
    $idRessource = test_input($_GET['idRessource']);
}

if (isset($_POST['idRessource'])){
    $idRessource = test_input($_POST['idRessource']);
}


if (isset($_POST['envoyer'])) {

    $filename = test_input($_FILES['file']['name']);
    $tmpname = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $file_type = $_FILES['file']['type'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);


    $fp = fopen($tmpname, 'r');
    $content = fread($fp, filesize($tmpname));
    $content = addslashes($content);
    fclose($fp);

    try {
        $ressourceExiste = !empty(ObjetMappeFactory::getViaId(Ressource::class, $idRessource));
    } catch (Exception $e) {
        $ressourceExiste = false;
    }

    if ($ressourceExiste) { //todo ici test éventuel compat type mime

        $fichier = new Fichier();
        $fichier->nom = $filename;
        $fichier->type = $file_type;
        $fichier->taille = $file_size;
        $fichier->donnees = $content;
        $fichier->id__ressource = $idRessource;

        if ($fichier->creerDansBase()) {
            $message = 'Le fichier a bien été envoyé.<br/>';
        } else {
            $message .= 'Erreur lors de l\'envoi du fichier.';
        }
    } else {
        $idAff=!isset($idRessource)?-1:$idRessource;
        $message .= "La ressource associée (n° {$idAff}) n'existe pas dans la base";
    }
}
?>

<?php include('entete.php'); ?>

<?php echo formaterMessage($message); ?>


    <div class="entiteCadree clearfix">
        <h3>Envoi d'un fichier</h3>

        <form action="fichierEnvoi.php" method="post" enctype="multipart/form-data">

            <div class="hautEntite">
                <div><input type="file" name="file" style="width: auto"/></div>
            </div>

            <div class="basEntite">
                <div><input type="submit" name="envoyer" value="envoyer" class="boutonVert"/></div>
            </div>


           <input type="hidden" name="idRessource" value="<?php echo $idRessource?>">

        </form>
    </div>


<?php include('piedPage.php');
?>