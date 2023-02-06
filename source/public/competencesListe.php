<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
} ?>
<?php
/**
 * Affiche la liste des compétences ou la liste de pré-requis qu'il est possible d'ajouter
 */


$messageErreur="";
$compsNonFiltrees = ObjetMappeFactory::getTous(Competence::class);
//Mode ajout de pré-requis si idCompCible !=0
if (isset($_GET['idCompCible']) && !empty($_GET['idCompCible'])) {
    $idCompCible = test_input($_GET['idCompCible']);

    try {
        $compCible = ObjetMappeFactory::getViaId(Competence::class, $idCompCible);
    } catch (Exception $e) {
        $messageErreur = "Erreur de chargement de la compétence cible";
        $idCompCible = 0; //on repart sur un affichage simple de la liste
        $compCible=new Competence();
    }

} else {
    $compCible=new Competence();
    $idCompCible = 0; //Sinon affichage de toutes les compétences pour consultation
}

if (isset($_GET['idRessCible']) && !empty($_GET['idRessCible'])) {
    $idRessCible = test_input($_GET['idRessCible']);

    try {
        $ressCible = ObjetMappeFactory::getViaId(Ressource::class, $idRessCible);
    } catch (Exception $e) {
        $messageErreur = "Erreur de chargement de la ressource cible";
        $idRessCible = 0; //on repart sur un affichage simple de la liste
        $ressCible=new Ressource();
    }

} else {
    $idRessCible = 0; //Sinon affichage de toutes les compétences pour consultation
    $ressCible=new Ressource();
}


//Recuperation des info du formulaire de filtrage
if (isset($_GET['submit'])) {
    $titre = test_input($_GET['titre']);
    $description = test_input($_GET['description']);
} else {
    $titre = "";
    $description = "";
}

//filtrage selon formulaire et compétence cible si spécifiée
$compFiltrees = array();
foreach ($compsNonFiltrees as $c) {
    //filtrage d'après champs

    //TODO factoriser la fonction de recherche
    $ajout=true;
    if(!empty($titre)) {
        $motsTitre = explode(" ", $titre);
        foreach ($motsTitre as $motTitre)
            $ajout = (contient(strtoupper(skip_accents($motTitre)), strtoupper(skip_accents($c->nom))));
    }

    if(!empty($description)) {
        $motsDesc = explode(" ",$description);
        foreach ($motsDesc as $motDesc){
            $ajout &= contient(strtoupper(skip_accents($motDesc)), strtoupper(skip_accents($c->description))) ;
        }
    }


    if (isset($compCible) && $idCompCible != 0) {
        //si recherche de pre-requis, on affiche pas la compétence cible
        $ajout &= $c->id != $idCompCible;

        //on affiche pas non plus les pré-requis existants de la compétence cible
        foreach ($compCible->tablePreRequis as $preRequis) {
            $ajout &= $c->id != $preRequis->id__competence;
        }

        foreach ($c->tablePreRequis as $preRequis) {
            $ajout &= $preRequis->id__competence != $idCompCible;
        }

    }


    if ($ajout) { //L'item correspond aux critères
        $compFiltrees[] = $c;
    }
}


//Selections des compétences à afficher dans $comps selon pagination
$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ITEMSPARPAGE; //TODO : parametrage
$total_count = sizeof($compFiltrees);
$pagination = new Pagination($page, $per_page, $total_count);
/**
 * !!! Chaine à modifier si mod $_GET
 */
$baseUrlPagination = "competencesListe.php?idCompCible={$idCompCible}&idRessCible={$idRessCible}&titre={$titre}&description={$description}&page=";


$rangCourant = 0;
$offSet = $pagination->getDecalage();
//selection des compétences à afficher suivant num page
$comps=array();
foreach ($compFiltrees as $c) {
    if ($rangCourant >= ($per_page + $offSet)) break;
    if ($rangCourant >= $offSet) $comps[] = $c;
    $rangCourant++;
}

//Selection du titre à afficher
if ($idCompCible == 0 && $idRessCible == 0 ) {
    $titrePage = "<h2>Liste des compétences ( {$total_count} sélectionnées)</h2>";
    $titrePage .= '<div><a href="competenceAjoutModif.php">Ajouter une compétence</a></div>';
} else {

    if ($idRessCible == 0 && $idCompCible != 0) {
        $titrePage = "<h2>Choisir des pré-requis à affecter à la compétence : </h2>";
        $titrePage .= "<h2>\"{$compCible->nom}\" ( {$total_count} sélectionnés)</h2>";
    } else {
        $titrePage = "<h2>Choisir une compétence à lier à la ressource : </h2>";
        $titrePage .= "<h2>\"{$ressCible->titre}\" ( {$total_count} sélectionnés)</h2>";
    }


}
?>
<?php include('entete.php'); ?>
<?php echo formaterMessage($messageErreur); ?>

<?php echo $titrePage ?>
<br/>

<br/>
<form action="competencesListe.php" method="get">
    <h4></h4>
    <div><label>Filtrage sur titre :
            <input type="text" name="titre" value="<?php echo $titre ?>"/>
        </label>

        <label>Filtrage sur description :<input type="text" name="description" value="<?php echo $description ?>"/>
        </label>
        <input type="submit" name="submit" value="Filtrer" class="boutonVert"/></div>
    <input type="hidden" name="idCompCible" value="<?php echo $idCompCible ?>">
</form>


<?php include('liensPagination.php'); ?>

<?php foreach ($comps as $comp): ?>

    <?php include('competenceInclusion.php'); ?>

<?php endforeach; ?>

<?php include('liensPagination.php');

include('piedPage.php'); ?>
