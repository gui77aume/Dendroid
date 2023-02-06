<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();

$message=$session->message();
if(empty($message)){
    redirigerVers('index.php');
}
?>

<?php
if(!$session->is_logged_in()) {
    $nePasAfficherMenu = true;
}
include('entete.php'); ?>

<br/>
<br/>
<br/>
<?php echo "<p>" . $message ."</p>";?>
<br/>
<br/>
<a href="index.php">Retour à l'accueil</a>


<?php


  //  echo MenuNav::getMenuEnColonne($session->is_logged_in());


include('piedPage.php'); ?>
