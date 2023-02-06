<?php require_once("../includes/init.php");


use PHPUnit\Framework\TestCase;

class WebTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

    }




    function testJoliesCompetences()
    {

        $descImp = "Implanter un ouvrage en repérant les niveaux, en respectant cotes, repères, et orientations. Tracer (niveaux, angles droits, aplombs, alignements) .Mettre en place les chaises.";
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


