<?php
//require_once(LIB_PATH . DS . 'BaseDonnees.php');

class Concerner extends RelationBinaire
{

//_concerner(
//id            Int NOT NULL ,
//id__ressource Int NOT NULL
//,CONSTRAINT _concerner_PK PRIMARY KEY (id,id__ressource)
//
//,CONSTRAINT _concerner__competence_FK FOREIGN KEY (id) REFERENCES _competence(id)
//,CONSTRAINT _concerner__ressource0_FK FOREIGN KEY (id__ressource) REFERENCES _ressource(id)
//)

    public $id; //id d'une compétence
    public $id__ressource; //

    //TODO : deplacer certaines methodes vers RelationBinaire

    public static function delierCompetencesDeRessource($idRessource): int
    {
        $res = 0;
        try {
            $liensAEffacer = ObjetMappeFactory::getMultipleViaChamp(Concerner::class, 'id__ressource', $idRessource);
            if (isset($liensAEffacer) && !empty($liensAEffacer)) {
                $res = true;
                foreach ($liensAEffacer as $lien) {
                    $res += $lien->effacerDeBase();
                }
            }
        } catch (Exception $e) {
        }
        ArbreCompetence::genererArbreJson();
        return $res;
    }


    public static function delierRessourcesDeCompetence($idCompetence): int
    {
        $res = 0;
        try {
            $liensAEffacer = ObjetMappeFactory::getMultipleViaChamp(Concerner::class, 'id', $idCompetence);
            if (isset($liensAEffacer) && !empty($liensAEffacer)) {

                foreach ($liensAEffacer as $lien) {
                    $res += $lien->effacerDeBase();
                }
            }
        } catch (Exception $e) {
        }
        ArbreCompetence::genererArbreJson();
        return $res;
    }


    public static function getCompetencesAssociees($idRessource): array
    {
        $res = array();
        try {
            $relations = ObjetMappeFactory::getMultipleViaChamp(Concerner::class, 'id__ressource', $idRessource);
            foreach ($relations as $r){
                try {
                    $res[]= ObjetMappeFactory::getViaId(Competence::class, $r->id);
                } catch (Exception $e) {
                    log_action("Concerner::getCompetences",$e->getMessage());
                }
            }

        } catch (Exception $e) {
            log_action("Concerner::getCompetences",$e->getMessage());
        }

        return $res;

    }

    public static function getRessourcesAssociees($idCompetence): array
    {
        $res = array();
        try {
            $relations = ObjetMappeFactory::getMultipleViaChamp(Concerner::class, 'id', $idCompetence);
            foreach ($relations as $r){
                try {
                    $res[]= ObjetMappeFactory::getViaId(Ressource::class, $r->id__ressource);
                } catch (Exception $e) {
                    log_action("Concerner::getRessourcesAssociees",$e->getMessage());
                }
            }

        } catch (Exception $e) {
            log_action("Concerner::getRessourcesAssociees",$e->getMessage());
        }

        return $res;

    }


    public static function lierCompetenceARessource($idCompetence, $idRessource): int
    {

        $relation = new Concerner();
        $relation->id__ressource = $idRessource;
        $relation->id = $idCompetence;
        $res= $relation->sauverDansBase();
        ArbreCompetence::genererArbreJson();
        return $res;

    }

    public static function delierCompetenceDeRessource($idCompetence, $idRessource): int
    {

        $res = 0;
        try {
            $lienAEffacer = ObjetMappeFactory::getViaDeuxChamps(Concerner::class, 'id__ressource', $idRessource,'id',$idCompetence);
            if (isset($lienAEffacer) && !empty($lienAEffacer)) {
                    return $lienAEffacer->effacerDeBase();

            }
        } catch (Exception $e) {
            log_action("delier compétence et ressource",$e->getMessage());
            $res=0;
        }
        ArbreCompetence::genererArbreJson();
        return $res;

    }



    public static function getNomsChampsBD(): array
    {
        return array(
            'id',
            'id__ressource',
        );
    }


    public static function getNomTable(): string
    {
        return "_concerner";
    }


    public function getDeuxiemeId(): array
    {

        return array('nom' => 'id__ressource', 'valeur' => $this->id__ressource);
    }


}

