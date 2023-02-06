<?php

class Ressource extends ObjetMappe
{
    public $id;
    public $titre;
    public $refQualite;
    public $validationQualite;
    public $description;
    public $URL;
    public $motsClefs;
    public $auteur;
    public $publique;
    public $obsolete;
    public $dateAjout;
    public $dateMAJ;
    public $conversionTexte;

    public function effacerDeBase(): int
    {


        $this->supprimerFichierAssocie();
        Concerner::delierCompetencesDeRessource($this->id);
        ArbreCompetence::genererArbreJson();
        return parent::effacerDeBase();
    }

    public function creerDansBase(): int
    {

        $res = parent::creerDansBase();
        ArbreCompetence::genererArbreJson();
        return $res;
    }

    public function majDansBase(): int
    {
        $res= parent::majDansBase();
        ArbreCompetence::genererArbreJson();
        return $res;
    }

    public function supprimerFichierAssocie(){
        try {
            $fichier = $this->getFichierSansDonnees();
            if($fichier) {
                $fichier->effacerDeBase();
                ArbreCompetence::genererArbreJson();
            }
        } catch (Exception $e) {
        }
    }







    public function getFichier()
    {

        try {
            $fichier = ObjetMappeFactory::getViaChamp(Fichier::class, 'id__ressource', $this->id);
        } catch (Exception $e) {
            $fichier = false;
        }
        return $fichier;
    }


    public function getFichierSansDonnees()
    {

        try {
            $fichier = ObjetMappeFactory::getViaChamp(FichierSansDonnees::class, 'id__ressource', $this->id);
        } catch (Exception $e) {
            $fichier = false;
        }
        return $fichier;
    }


    public static function getNomsChampsBD(): array
    {
        return array(

            'id',
            'titre',
            'refQualite',
            'validationQualite',
            'description',
            'URL',
            'motsClefs',
            'auteur',
            'publique',
            'obsolete',
            'dateAjout',
            'dateMAJ',
            'conversionTexte');
    }


    public static function getNomTable(): string
    {
        return "_ressource";
    }

    public static function indexAutoIncrement(): bool
    {
        return true;
    }

}

