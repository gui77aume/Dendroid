<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) {
    redirigerVers("connexion.php");
} ?>
<?php
/**
 * Affiche la liste des ressources
 */
$messageErreur="";
$ressNonFiltrees = ObjetMappeFactory::getTous(Ressource::class);



//Mode ajout de ressource à une comp si idCompCible !=0
if (isset($_GET['idCompCible']) && !empty($_GET['idCompCible'])) {
    $idCompCible = test_input($_GET['idCompCible']);

    try {
        $compCible = ObjetMappeFactory::getViaId(Competence::class, $idCompCible);
    } catch (Exception $e) {
        $messageErreur = "Erreur de chargement de la compétence cible";
        $idCompCible = 0; //on repart sur un affichage simple de la liste
    }

} else {
    $idCompCible = 0; //Sinon affichage de toutes les ressources pour consultation
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
$ressFiltrees = array();
foreach ($ressNonFiltrees as $r) {
    //filtrage d'après champ
    $ajout=true;
    if(!empty($titre)) {
        $motsTitre = explode(" ", $titre);
        foreach ($motsTitre as $motTitre)
            $ajout = (contient(strtoupper(skip_accents($motTitre)), strtoupper(skip_accents($r->titre))));
    }

    if(!empty($description)) {
        $motsDesc = explode(" ",$description);
        foreach ($motsDesc as $motDesc){
            $ajout &= contient(strtoupper(skip_accents($motDesc)), strtoupper(skip_accents($r->description))) ;
        }
    }

    if (isset($compCible) && $idCompCible != 0) {

      $ressources = Concerner::getRessourcesAssociees($idCompCible);

        //on affiche pas les ressources déjà liées à la compétence cible
        foreach ($ressources as $ressourceDejaLiee) {
            $ajout &= $r->id != $ressourceDejaLiee->id ;
        }


    }


    if ($ajout) { //L'item correspond aux critères
        $ressFiltrees[] = $r;
    }
}


//Selections des ress à afficher dans $ress selon pagination
$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = ITEMSPARPAGE; //TODO : parametrage
$total_count = sizeof($ressFiltrees);
$pagination = new Pagination($page, $per_page, $total_count);
/**
 * !!! Chaine à modifier si mod $_GET
 */
$baseUrlPagination = "ressourceListe.php?idCompCible={$idCompCible}titre={$titre}&description={$description}&page=";

$rangCourant = 0;
$offSet = $pagination->getDecalage();
//selection des compétences à afficher suivant num page
$ressources=array();
foreach ($ressFiltrees as $r) {
    if ($rangCourant >= ($per_page + $offSet)) break;
    if ($rangCourant >= $offSet) $ressources[] = $r;
    $rangCourant++;
}

//Selection du titre à afficher
if ($idCompCible == 0) {
    $titrePage = "<h2>Liste des ressources ( {$total_count} sélectionnées)</h2>";
    $titrePage .= '<div><a href="ressourceAjoutModif.php">Ajouter une ressource</a></div>';
} else {
    $titrePage = "<h2>Choisir une ressource à affecter à la compétence : </h2>";
    $titrePage .= "<h2>\"{$compCible->nom}\" ( {$total_count} sélectionnés)</h2>";
}





?>
<?php include('entete.php'); ?>
<?php echo formaterMessage($messageErreur); ?>

<?php echo $titrePage ?>
<br/>

<br/>
<form action="ressourcesListe.php" method="get">
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

<?php foreach ($ressources as $ress): ?>

    <?php
    $fichier = $ress->getFichierSansDonnees();
    include('ressourceInclusion.php'); ?>

<?php endforeach; ?>

<?php include('liensPagination.php');

include('piedPage.php'); ?>
