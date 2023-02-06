<?php
require_once("../includes/init.php");
$session = Session::getInstance();
$messageErreur="";
if (isset($_POST['submit'])) { //Procedure de login d'après id pass fournis

    $nom = test_input($_POST['nom']);
    $prenom = test_input($_POST['prenom']);
    $email = test_input($_POST['email']);
    $username = test_input($_POST['username']);
    $password = test_input($_POST['password']);
    $remdp = test_input($_POST['remdp']);

    $erreur = false; //à voir : supprimer cette var et utiliser $message pour detection err ?
    $messageErreur = "";

    //champs complétés ?
    if (empty($_POST["nom"])
        || empty($_POST["prenom"])
        || empty($_POST["email"])
        || empty($_POST["username"])
        || empty($_POST["password"])
        || empty($_POST["remdp"])) {
        $erreur = true;
        $messageErreur .= "Merci de compléter l'intégralité des champs demandés.<br/>";
    }

    if (!preg_match("/^[a-z0-9]*$/", $username)) {
        $messageErreur .= "Merci de ne pas utiliser de caractères spéciaux ni de majuscules pour votre identifiant.<br/>";
        $erreur = true;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageErreur .= "L'email saisi ne semble pas correct.<br/>";
        $erreur = true;
    }

    //Existence de l'identifiant ?
    try {
        $user = ObjetMappeFactory::getViaChamp(Utilisateur::class, 'login', $username);
        if (!empty($user)) {
            $messageErreur .= "Cet identifiant est déjà utilisé, merci d'en choisir un nouveau.<br/>";
            $erreur = true;
        }
    } catch (Exception $e) {
    }

    if (!$remdp == $password) {
        $messageErreur .= "Les mots de passe sont différents.<br/>";
        $erreur = true;

    }

    if (!$erreur) {

        $util = new Utilisateur();
        $util->login = $username;
        $util->hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $util->prenom = $prenom;
        $util->nom = $nom;
        $util->email = $email;
        $util->droits = '1';
        $util->droitsValides = '0';
        $util->emailVerifie = '0';
        $util->initJeton();
        if ($util->sauverDansBase()) {
            $util->envoyerMailVerif();
            //todo : procedure effacement si mail non validé
            $message = "Merci !<br/> Pour terminer votre inscription, il ne vous reste qu'à valider votre email (merci de vérifier vos spams si besoin).";
        } else {
            $message = "Erreur lors de la création du nouvel utilisateur.";
        }

    }


} else { // Form non soumis
    $nom = "";
    $prenom = "";
    $email = "";
    $username = "";
    $password = "";
    $remdp = "";
}

?>

<?php if (isset ($message) && !empty($message)) {
    $session->message($message);
    redirigerVers('redirectionMessage.php');

}
?>

<?php include('entete.php'); ?>


<?php echo formaterMessage($messageErreur); ?>

<script type="text/javascript">

    async function searchId() {
        const label = document.getElementById("labelUsername");
        const textInput = document.getElementById("usernameId");
        const login = document.getElementById("usernameId").value;

        const reponse = await fetch("loginValide.php?login=" + login, {method: 'GET'});
        const rep = await reponse.text();

        if (rep === "true") {
            textInput.setAttribute("style", "background-color: lightgreen;");
            label.innerText = "Identifiant valide"
        } else {

            if (rep === "false") textInput.setAttribute("style", "background-color: lightsalmon;");
            label.innerText = "Identifiant invalide ou déjà utilisé"
        }
    }


</script>

<div class="entiteCadree clearfix">
    <h3>Inscription</h3>

    <form action="inscription.php" method="post">

        <div class="hautEntite">

            <div>Prénom</div>
            <div><input type="text" name="prenom" value="<?php echo $prenom ?>"/></div>
            <br/>

            <div>Nom</div>
            <div><input type="text" name="nom" value="<?php echo $nom ?>"/></div>
            <br/>


            <div>Email</div>
            <div><input type="text" name="email" value="<?php echo $email ?>"/></div>
            <br/>

            <div id="labelUsername">Identifiant</div>
            <div><input type="text" name="username" id="usernameId" onchange="searchId()" onkeyup="searchId()"
                        value="<?php echo $username ?>"/>

            </div>
            <br/>

            <div>Mot de passe</div>
            <div><input type="password" name="password" value="<?php echo $password ?>"/></div>
            <br/>

            <div>Confirmer le mot de passe</div>
            <div><input type="password" name="remdp" value="<?php echo $remdp ?>"/></div>

        </div>

        <div class="basEntite">
            <div><input type="submit" name="submit" value="S'inscrire" class="boutonVert"/></div>
        </div>

    </form>
</div>
<?php include('piedPage.php');
?>
