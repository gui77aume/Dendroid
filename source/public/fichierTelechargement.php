<?php

require_once("../includes/init.php");
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
}

//$_GET['id']=4; pour test
if (isset($_GET['id'])) {
    $id = test_input($_GET['id']);
    try {
        $fichier = ObjetMappeFactory::getViaId(Fichier::class, $id);
        header("Content-length: $fichier->taille");
        header("Content-type: $fichier->type");
        header("Content-Disposition: attachment; filename=\"$fichier->nom\"");
        ob_clean();
        flush();
        echo $fichier->donnees;
    } catch (Exception $e) {
        log_action("Erreur lors de l'envoi du fichier",$e->getMessage());
    }

    exit;
}