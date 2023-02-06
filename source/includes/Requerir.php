<?php
//require_once(LIB_PATH . DS . 'BaseDonnees.php');

class Requerir extends RelationBinaire
{


    public $id; //id d'une compétence
    public $id__competence; // id d'un prérequis

    public function sauverDansBase(): int
    {

        $res = parent::sauverDansBase();
        ArbreCompetence::genererArbreJson();
        return $res;
    }

    public function effacerDeBase(): int
    {
        $res = parent::effacerDeBase();
        ArbreCompetence::genererArbreJson();
        return $res;
    }

    public static function getNomsChampsBD(): array
    {
        return array(
            'id',
            'id__competence',
        );
    }


    public static function getNomTable(): string
    {
        return "_requerir";
    }


    public function getDeuxiemeId(): array
    {

        return array('nom' => 'id__competence', 'valeur' => $this->id__competence);
    }
}

