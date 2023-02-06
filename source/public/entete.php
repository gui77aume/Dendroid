<?php
require_once("../includes/init.php"); ?>


<?php
//Template à inclure en haut de page
$session = Session::getInstance();
if (isset($nePasAfficherMenu) && $nePasAfficherMenu) {
    $menu = "";
} else {
    $menu = "<menu>" .MenuNav::getMenuEnLigne($session->is_logged_in()) .  "</menu>";
}
if ($session->is_logged_in()) {

//    $menu .= '<a href="graph/graph.php">Parcourir les compétences</a>';
//    $menu .= '<a href="competencesListe.php">Gérer les compétences</a>';
//    $menu .= '&nbsp;&nbsp;&nbsp;<a href="logout.php">Déconnexion</a>';

    try {
        $util = ObjetMappeFactory::getViaId(Utilisateur::class, $session->user_id);
        if ($util->droits == 0) {
            $menu .= '<a href=' . RACINE . ':9000>Administration</a>&nbsp' . $menu;
        }
    } catch (Exception $e) {
        log_action("Erreur lors de la lecture des informations utilisateur",$e->getMessage());
    }
    
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Système de gestion de ressources pédagogiques</title>
    <link href="css/main.css" media="all" rel="stylesheet" type="text/css"/>

</head>
<body>
<div class="container">
    <header>
        <h1><strong>Dendroïde</strong></h1>
        <h2>Ingénierie pédagogique</h2>

    </header>
    <?php echo $menu ?>
    <br/>

    <section class="main">
