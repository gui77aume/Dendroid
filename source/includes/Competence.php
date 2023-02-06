<?php

class Competence extends ObjetMappe
{
    public $id;
    public $nom;
    public $nomCourt;
    public $description;
    public $tablePreRequis;

    function __construct($enregistrement = "")
    {
        parent::__construct($enregistrement);
        $this->chargerPreRequis();
    }


    /**
     * On doit délier une compétence des autres avant de l'effacer
     * @return int
     */
    public function effacerDeBase(): int
    {
        //TODO :  implémenter une version append-only ?
        $this->NePlusAvoirPreRequis();
        $this->nePlusEtrePreRequis();
        Concerner::delierRessourcesDeCompetence($this->id);
        $res = parent::effacerDeBase(); //régénère arbre
        ArbreCompetence::genererArbreJson();
        return $res;
    }

    public function NePlusAvoirPreRequis(): int
    {
        $res = 0;
        foreach ($this->tablePreRequis as $pr) {
            $res += $pr->effacerDeBase(); //régénère arbre
        }

        return $res;
    }


    public function NePlusAvoirCePreRequis($preRequisAEnlever): int
    {

        foreach ($this->tablePreRequis as $pr) {
            if ($pr->id__competence == $preRequisAEnlever->id) {
                return $pr->effacerDeBase(); //régénère arbre
            }

        }
        return 0;
    }

    public function nePlusEtrePreRequis(): int
    {
        $res = 0;
        try {
            $preRequisAEffacer = ObjetMappeFactory::getMultipleViaChamp(Requerir::class, 'id__competence', $this->id);
            if (isset($preRequisAEffacer) && !empty($preRequisAEffacer)) {

                foreach ($preRequisAEffacer as $pr) {
                    $res += $pr->effacerDeBase(); //régénère arbre
                }
            }
        } catch (Exception $e) {
            //$this->tablePreRequis = array(); //TODO: vérifier ce cas
        }
        return $res;
    }

    /**
     * @param $prerequis
     * @return int
     * @throws Exception
     */
    public function ajouterPreRequis($prerequis): int
    {
        //TODO : check cycle graph
//        $this->sauverDansBase(); //premet de creer id si nouvelle compétence
        if (!isset($this->id) || $this->id == 0) throw new Exception("Tentative d'ajouter un prérequis à une compétence non enregistrée dans la base. Veuillez d'abord sauvegarder la compétence.");

        $pr = new Requerir();
        $pr->id = $this->id;
        $pr->id__competence = $prerequis->id;
        $res = $pr->sauverDansBase(); //régénère arbre
        $this->tablePreRequis[] = $pr;
        //$this->chargerPreRequis(); au choix...
        return $res;
    }

    private function chargerPreRequis()
    {
        try {
            $this->tablePreRequis = ObjetMappeFactory::getMultipleViaChamp(Requerir::class, 'id', $this->id);

        } catch (Exception $e) {
            log_action("chargerPreRequis", $e->getMessage());
            $this->tablePreRequis = array();
        }

    }

    public function getPreRequis(): array
    {
        $res = array();
        foreach ($this->tablePreRequis as $preRequis) {
            try {
                $res[] = ObjetMappeFactory::getViaId(Competence::class, $preRequis->id__competence);
            } catch (Exception $e) {
                log_action("getPreRequis", $e->getMessage());
            }
        }
        return $res;
    }


    public function majDansBase(): int
    {
        $res = parent::majDansBase();
        ArbreCompetence::genererArbreJson();
        return $res;

    }

    public function creerDansBase(): int
    {
        $res = parent::creerDansBase();
        ArbreCompetence::genererArbreJson();
        return $res;

    }


    public static function getNomsChampsBD(): array
    {
        return array(
            'id',
            'nom',
            'nomCourt',
            'description');
    }


    public static function getNomTable(): string
    {
        return "_competence";
    }

    public static function indexAutoIncrement(): bool
    {
        return true;
    }

}

