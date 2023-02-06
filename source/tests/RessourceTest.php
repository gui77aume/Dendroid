<?php require_once("../includes/init.php");


use PHPUnit\Framework\TestCase;

class RessourceTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

    }

    private function creerFichier($idRessource)
    {
        $fichier = new Fichier();
        $fichier->id__ressource = $idRessource;
        $fichier->nom = "test";
        $fichier->type = "-";
        $fichier->taille = 1;
        $fichier->donnees = "1";
        $fichier->sauverDansBase();
    }

    function testLiaisonCompetenceEtFichier()
    {
        $comp3 = $this->creerComp(3);
        $comp4 = $this->creerComp(4);
        $ressource = $this->creerRessource(3);
        $this->creerFichier($ressource->id);


        self::assertEquals(1, Concerner::lierCompetenceARessource($comp3->id, $ressource->id));
        self::assertEquals(1, Concerner::lierCompetenceARessource($comp4->id, $ressource->id));

        $compResArray = Concerner::getCompetencesAssociees($ressource->id);


        $test = $compResArray[0]->nom == $comp3->nom && $compResArray[1]->nom == $comp4->nom;
        $test |= $compResArray[0]->nom == $comp4->nom && $compResArray[1]->nom == $comp3->nom;
        self::assertTrue($test == true); //pfff

        $resArray = Concerner::getRessourcesAssociees($comp3->id);
        self::assertEquals("Un exemple de ressource n° 3",$resArray[0]->titre);


        self::assertEquals(1, $ressource->effacerDeBase());

        //on recrée et lie à nouveau la ressource pour tester la suppression des comps avant
        $ressource = $this->creerRessource(3);
        $this->creerFichier($ressource->id);
        self::assertEquals(1, Concerner::lierCompetenceARessource($comp3->id, $ressource->id));
        self::assertEquals(1, Concerner::lierCompetenceARessource($comp4->id, $ressource->id));

//        self::assertEquals(1,$comp3->effacerDeBase());
//        self::assertEquals(1,$comp4->effacerDeBase());
//        self::assertEquals(1,$ressource->effacerDeBase());

    }

    function testCreationRessource()
    {

        $ressource = $this->creerRessource(3);
        $ressource->sauverDansBase();
        self::assertTrue($ressource->effacerDeBase() == 1, "effacement 1");
        self::assertTrue($ressource->sauverDansBase() == 1, "sauvegarde 3");
        self::assertTrue($ressource->effacerDeBase() == 1, "effacement 2");


    }

    private function creerRessource($i): Ressource
    {
        $ressource = new Ressource();

        $ressource->titre = "Un exemple de ressource n° " . $i;
        $ressource->description = "La description de la ressource";
        $ressource->auteur = "John & Jane Doe";
        $ressource->conversionTexte = "Le contenu du fichier pdf converti en text pour les recherches";
        $ressource->dateAjout = date('Y-m-d H:i:s', strtotime("-10 day"));
        $ressource->dateMAJ = date('Y-m-d H:i:s', strtotime("-5 day"));
        $ressource->motsClefs = "exemple ressource";
        $ressource->obsolete = "0";
        $ressource->publique = "0";
        $ressource->refQualite = "2021LAISNEY12_" . $i;
        $ressource->URL = "www.unexemple.org";
        $ressource->validationQualite = "0";

        try {
            ObjetMappeFactory::getViaChamp(Ressource::class, 'titre', $ressource->titre)->effacerDeBase();
        } catch (Exception $e) {
        }

        $n = Ressource::nombreEntrees();
        self::assertEquals(1, $ressource->sauverDansBase());
        self::assertEquals($n + 1, Ressource::nombreEntrees());
        return $ressource;

    }


    private function creerComp($i): Competence
    {
        $this->effaceCompSiExiste($i);
        $comp = $this->creeInstanceComp($i);
        $n = Competence::nombreEntrees();
        $comp->sauverDansBase();
        self::assertEquals($n + 1, Competence::nombreEntrees());
        return $comp;
    }

    private function creeInstanceComp($num = '3'): Competence
    {
        $comp = new Competence();
        $comp->nom = "Compétence test n°{$num}";
        $comp->nomCourt = "n{$num}";
        $comp->description = "Une compétence crée pour les tests  n°{$num}- peut être supprimée sans problème";
        return $comp;
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

}


