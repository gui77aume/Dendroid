<?php require_once("../includes/init.php");


use PHPUnit\Framework\TestCase;

class CompetenceTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

    }

    public function testCreationCompetence()
    {
        $this->effaceCompSiExiste(3);
        $comp = $this->creeInstanceComp(3);
        $n = Competence::nombreEntrees();
        $comp->sauverDansBase();
        self::assertEquals($n + 1, Competence::nombreEntrees());

    }



//    public function testPR(){
//        for ($i = 11; $i <= 15; $i++) {
//            self::assertTrue($this->creerPreRequis(10, $i));
//            $this->creerPreRequis(10, $i);
//
//        }
//    }

    public function testCreationSuppressionPreRequis()
    {
        $this->creerComp(2);
        $this->creerComp(3);
        self::assertTrue($this->creerPreRequis(2, 3));
        //+verif

        try {
            $comp = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°2");
            $compPR = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°3");
        } catch (Exception $e) {
            $this->fail("Exception inattendue");
        }

        foreach ($comp->tablePreRequis as $pr) {
            $res = false;
            if ($pr->id__competence == $compPR->id) {
                $res = true;
            }
            if (!$res) self::fail("Le PR n'a pas été ajouté");
        }

        self::assertTrue($this->supprimerPreRequis(2, 3));


        try {
            $comp = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°2");
            $compPR = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°3");
        } catch (Exception $e) {
            $this->fail("Exception inattendue");
        }

        foreach ($comp->tablePreRequis as $pr) {
            if ($pr->id__competence == $compPR->id) {
                self::fail("Le PR n'a pas été supprimé");
            }
        }


    }

    function testJoliesCompetences()
    {

        $descImp = "Implanter un ouvrage en repérerant les niveaux, en respectant cotes, repères, et orientations. Tracer (niveaux, angles droits, aplombs, alignements) .Mettre en place les chaises.";
        $descDeco = " -Situer les ouvrages dans l’environnement
-Interpréter les traits, les écritures, les symboles de représentation
-Localiser un élément sur les différents dessins, plans
-Identifier et désigner la forme géométrique des surfaces et des volumes constitutifs des ouvrages
-Extraire les éléments utiles d’un plan
-Interpréter les cotations particulières";
        $descMaint = "Nettoyer les outillages individuels et collectifs après utilisation (journalière ou occasionnelle). Nettoyer et maintenir en état d’utilisation les matériels et véhicules après usage. Ranger et  stocker les outillages et matériels.";


        $implantation = $this->creerSauverJolieComp("C3.1 IMPLANTER UN OUVRAGE", "Implanter", $descImp);
        $maintenance = $this->creerSauverJolieComp("C2.3 MAINTENIR LE MATÉRIEL EN ÉTAT", 'Maintenir_materiel', $descMaint);
        $decoder = $this->creerSauverJolieComp("C1.1 DÉCODER DES DESSINS ET PLANS", "Decoder_dessin", $descDeco);
        $mediactice = $this->creerSauverJolieComp('Tracer la médiatrice d’un segment', 'Mediatrice', 'Tracer la médiatrice d’un segment aux instruments puis à l\'aide d\'un outil numérique');
        $perpPoint = $this->creerSauverJolieComp('Tracer une perpendiculaire à une droite, passant par un point', 'Perpendiculaire_specifique', 'Tracer une perpendiculaire passant par un point donné aux instruments puis à l\'aide d\'un outil numérique');

        try {
            $perpPoint->ajouterPreRequis($mediactice);
            $implantation->ajouterPreRequis($decoder);
            $implantation->ajouterPreRequis($maintenance);
            $implantation->ajouterPreRequis($perpPoint);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

    }

    function testCreationCompetencesNombreusesAvecPR()
    {
        for ($i = 10; $i <= 18 ; $i++) {
            $this->creerComp($i);
        }

        for ($i = 11; $i <= 15; $i++) {
            self::assertTrue($this->creerPreRequis(10, $i));
        }


            self::assertTrue($this->creerPreRequis(16, 10));


        for ($i = 17; $i <= 18; $i++) {
            self::assertTrue($this->creerPreRequis($i, 16));
        }
//todo verif pr bien enregistres

    }


    public function testCreationCompetenceAvecPreRequis()
    {
        //Création de 2 compétences

        $this->effaceCompSiExiste(4);
        $this->effaceCompSiExiste(3);
        $comp1 = $this->creeInstanceComp(3);
        $comp2 = $this->creeInstanceComp(4);
        $n = Competence::nombreEntrees();


        //ajout d'un pr sans sauvegarde de la nouvelle comp, doit échouer
        try {
            $comp1->ajouterPreRequis($comp2);
            $this->fail("Une exception devrait être levée au moment d'ajouter un pré-requis car les compétences cibles n'ont pas  d'id");
        } catch (Exception $e) {
            self::assertEquals("Tentative d'ajouter un prérequis à une compétence non enregistrée dans la base. Veuillez d'abord sauvegarder la compétence.", $e->getMessage());

        }

        //ajout d'un pr après sauvegarde
        $this->assertTrue($comp1->sauverDansBase());
        $this->assertTrue($comp2->sauverDansBase());
        self::assertEquals($n + 2, Competence::nombreEntrees());

        try {
            self::assertTrue($comp1->ajouterPreRequis($comp2));
            // $comp1->sauverDansBase(); inutile
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->fail("Exception inattendue au moment d'ajouter un pré-requis. La compétence cible a-t-elle un id ?");
        }


        //vérif ds base
        try {
            $comp1Extraite = ObjetMappeFactory::getViaId(Competence::class, $comp1->id);
            // var_dump($comp1Extraite);
            self::assertEquals($comp1Extraite->tablePreRequis[0]->id, $comp1->id);
            self::assertEquals($comp1Extraite->tablePreRequis[0]->id__competence, $comp2->id);

        } catch (Exception $e) {
            echo $e->getMessage();
            $this->fail("Exception lors du chargement de la compétence créée");

        }

        $this->effaceCompSiExiste(4);
        $this->effaceCompSiExiste(3);


    }

    private function creerPreRequis($numComp, $numPR): bool
    {

        try {
            $comp = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°{$numComp}");
            $compPR = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°{$numPR}");
        } catch (Exception $e) {
            return false;
        }

        return $comp->ajouterPreRequis($compPR);

    }

    private function supprimerPreRequis($numComp, $numPR): bool
    {

        try {
            $comp = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°{$numComp}");
            $compPR = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°{$numPR}");
        } catch (Exception $e) {
            return false;
        }

        return $comp->NePlusAvoirCePreRequis($compPR);

    }


    private function effaceCompSiExiste($num = '3')
    {
        try {
            $comp = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', "Compétence test n°{$num}");
        } catch (Exception $e) {

        }
        if (!empty($comp)) {
            $ncomp = Competence::nombreEntrees();
            $comp->effacerDeBase();
            self::assertEquals($ncomp - 1, Competence::nombreEntrees());
        }

    }

    private function effaceCompSiExisteParNom($nom )
    {
        if(empty($nom)) return;

        try {
            $comp = ObjetMappeFactory::getViaChamp(Competence::class, 'nom', $nom);
        } catch (Exception $e) {

        }
        if (!empty($comp)) {
            $ncomp = Competence::nombreEntrees();
            $comp->effacerDeBase();
            self::assertEquals($ncomp - 1, Competence::nombreEntrees());
        }

    }




    private function creerSauverJolieComp($nom, $nomCourt, $desc): Competence
    {
        $this->effaceCompSiExisteParNom($nom);
        $comp = new Competence();
        $comp->nom = $nom;
        $comp->nomCourt = $nomCourt;
        $comp->description = $desc;
        $n = Competence::nombreEntrees();
        $comp->sauverDansBase();
        self::assertEquals($n + 1, Competence::nombreEntrees());
        return $comp;
    }

    private function creerComp($i)
    {
        $this->effaceCompSiExiste($i);
        $comp = $this->creeInstanceComp($i);
        $n = Competence::nombreEntrees();
        $comp->sauverDansBase();
        self::assertEquals($n + 1, Competence::nombreEntrees());
    }

    private function creeInstanceComp($num = '3'): Competence
    {
        $comp = new Competence();
        $comp->nom = "Compétence test n°{$num}";
        $comp->nomCourt = "n{$num}";
        $comp->description = "Une compétence crée pour les tests  n°{$num}- peut être supprimée sans problème";
        return $comp;
    }


}


