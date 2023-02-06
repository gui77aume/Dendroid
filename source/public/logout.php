<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
    $session->logout();
    redirigerVers("connexion.php");
?>
