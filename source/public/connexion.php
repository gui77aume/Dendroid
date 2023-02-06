<?php
require_once("../includes/init.php");
$session = Session::getInstance();
if ($session->is_logged_in()) {
    redirigerVers("index.php");
}

$message="";

if (isset($_POST['submit'])) { //Procedure de login d'après id pass fournis

    $username = test_input($_POST['username']);
    $password = test_input($_POST['password']);
    $authOK = false;


    try {
        $user =  ObjetMappeFactory::getViaChamp(Utilisateur::class,'login', $username);
        if (!empty($user)) {
            $authOK = $user->authentifier($password) && $user->emailVerifie==1 &&  $user->droitsValides == '1';

        }

    } catch (Exception $e) {
        $authOK = false;
        $user=new Utilisateur();
    }


    if ($authOK) {
        $session->login($user);
        log_action('Login', "{$user->login} s'est connecté.");
        redirigerVers("index.php");
    } else {
        log_action('Login', "{$user->login} : erreur de connexion.");
        $message = "Erreur d'authentification.";
    }

} else { // Form non soumis
    $username = "";
    $password = "";
}

?>
<?php //inclureLayoutTemplate('entete.php');

$nePasAfficherMenu=true;
include('entete.php'); ?>


<?php echo formaterMessage($message); ?>


<div class="entiteCadree clearfix">
    <h3>Connexion</h3>

    <form action="connexion.php" method="post">

    <div class="hautEntite">
            <div>Identifiant</div>
            <div><input type="text" name="username" maxlength="30" value="<?php echo htmlentities($username); ?>"/>
            </div>
            <br/>
            <div>Mot de passe</div>
            <div><input type="password" name="password" maxlength="30" value="<?php echo htmlentities($password); ?>"/>
            </div>
    </div>
    <div class="basEntite">
        <div><input type="submit" name="submit" value="Connexion" class="boutonVert"/></div>
        <div><a href="inscription.php">Inscription</a></div>
    </div>

    </form>
</div>
<?php include('piedPage.php');


?>
