<?php

require_once(LIB_PATH . DS . 'BaseDonnees.php');

abstract class ObjetMappe
{
    //ATTENTION CETTE CLASSE NE FONCTIONNE QUE POUR LES OBJETS A CLEF PRIMAIRE UNIQUE DE NOM id


    //Patron Template Method : cette classe utilise les méthodes abstraites définies par les classes filles
    abstract public static function getNomTable();
    abstract public static function indexAutoIncrement();
    abstract public static function getNomsChampsBD();

    public function __construct($enregistrement = "")
    {
        if (!empty($enregistrement) && is_array($enregistrement)) {
            foreach ($enregistrement as $attribute => $value) {
                if ($this->possedeAttribut($attribute)) {
                    $this->$attribute = $value;
                }
            }
        }
    }



//seule fonction statique
    public static function nombreEntrees(): int
    {
        $database = BaseDonnees::getInstance();
        $sql = "SELECT COUNT(*) FROM " . static::getNomTable();
        $result_set = $database->requete($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }




    public function sauverDansBase(): int
    {

//        log_action("sauverDansBase",$this->id);
        //si les indexes sont gérés par la base, on peut se fier à l'index de l'objet
        if (static::indexAutoIncrement()) {
            $res = isset($this->id) ? $this->majDansBase() : $this->creerDansBase();
        } else { //TODO ne fonctionne pas ?
            //sinon il faut interroger la base
            $existe = $this->existeDansLaBase();
            $res = $existe ? $this->majDansBase() : $this->creerDansBase();
        }
       // if(!$res) var_dump($this);
        return $res;
    }
    //todo : faire comme ici https://stackoverflow.com/questions/45689582/if-not-exists-mariadb-syntax ? mais le code sera spécifique mysql/mariadb
//INSERT INTO valuation (ticker, depot_id, src_id, valuation_date, value)
//VALUES ('BK001EUR', 1, 2, '2009-09-09', 14999260.46)
//ON DUPLICATE KEY UPDATE value = VALUES(value);

//AUTRE SOLUTION PLUS BRUTALE  Pourrait fonctionner en gérant mieux les erreurs SQL (un die() empêche ce type de fonctionnement pour l'instant)
//    public function sauverDansBase(): bool{
//        if(!$res = $this->creerDansBase()) $res = $this->majDansBase();
//        return $res;
//    }




    public function creerDansBase(): int
    {
        $database = BaseDonnees::getInstance();
        $attributs = $this->getAttributsEchappesSql();

        if (static::indexAutoIncrement()) unset($attributs['id']);

        $sql = "INSERT INTO " . $this->getNomTable() . " (";
        $sql .= join(", ", array_keys($attributs));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributs));
        $sql .= "')";
        if ($database->requete($sql)) {
            $this->id = $database->insertId();
        }
        return $database->nRangsAffectes();
    }

    public function majDansBase(): int
    {
        $database = BaseDonnees::getInstance();
        $attributs = $this->getAttributsEchappesSql();
        $attribute_pairs = array();
        foreach ($attributs as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . $this->getNomTable() . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id=" . $database->echapper($this->id);
        $database->requete($sql);
        return $database->nRangsAffectes() ;
    }

    public function effacerDeBase(): int
    {
        $database = BaseDonnees::getInstance();
        $sql = "DELETE FROM " . $this->getNomTable();
        $sql .= " WHERE id=" . $database->echapper($this->id);
        $sql .= " LIMIT 1";
        $database->requete($sql);
        $res = $database->nRangsAffectes();
        if ($res>=1) $this->id=null;
        return $res;
    }







    public function possedeAttribut($attribute): bool
    {
        return array_key_exists($attribute, $this->getAttributs());
    }

    protected function getAttributs(): array
    {
        $attributs = array();
        foreach ($this->getNomsChampsBD() as $field) {
            if (property_exists($this, $field)) {
                $attributs[$field] = $this->$field;
            }
        }
        return $attributs;
    }

    protected function getAttributsEchappesSql(): array
    {
        $database = BaseDonnees::getInstance();
        $clean_attributs = array();
        foreach ($this->getAttributs() as $key => $value) {
            $clean_attributs[$key] = $database->echapper($value);
        }
        return $clean_attributs;
    }


    private function existeDansLaBase(): bool
    {

        $database = BaseDonnees::getInstance();
        $sql = "SELECT COUNT(*) FROM " . static::getNomTable() . " WHERE id={$this->id} LIMIT 1";
        $result_set = $database->requete($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row) != 0;

    }

}


