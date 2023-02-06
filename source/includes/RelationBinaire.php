<?php

require_once(LIB_PATH . DS . 'BaseDonnees.php');

abstract class RelationBinaire extends ObjetMappe
{

//Cas particulier des relations pour lesquelles l'identifiant est constitué de deux champs.
//Attention il peut aussi exister des attributs de relation


    abstract public function getDeuxiemeId(); //renvoie un array nom,valeur pour le 2eme id de l'association

    public static function indexAutoIncrement(): bool
    {
        return false;
    }

    public function sauverDansBase(): int
    {
        //si les indexes sont gérés par la base, on peut se fier à l'index de l'objet
        $existe = $this->existeDansLaBase();
        return $existe ? $this->majDansBase() : $this->creerDansBase();

    }

    public function majDansBase(): int //utile pour relation avec contenu
    {
        $nomId2 = static::getDeuxiemeId()['nom'];
        $valeurId2 = static::getDeuxiemeId()['valeur'];

        $database = BaseDonnees::getInstance();
        $attributes = $this->getAttributsEchappesSql();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . $this->getNomTable() . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id=" . $database->echapper($this->id) ." and {$nomId2}={$valeurId2}";
        $database->requete($sql);
        return $database->nRangsAffectes();
    }


    public function effacerDeBase(): int
    {
        $nomId2 = $this->getDeuxiemeId()['nom'];
        $valeurId2 = $this->getDeuxiemeId()['valeur'];

        $database = BaseDonnees::getInstance();
        $sql = "DELETE FROM " . $this->getNomTable();
        $sql .= " WHERE id=" . $database->echapper($this->id);
        $sql .= " and {$nomId2}={$valeurId2}";
        $sql .= " LIMIT 1";
        $database->requete($sql);
        return $database->nRangsAffectes() ;

    }


    private function existeDansLaBase(): bool
    {
        $nomId2 = static::getDeuxiemeId()['nom'];
        $valeurId2 = static::getDeuxiemeId()['valeur'];

        $database = BaseDonnees::getInstance();
        $sql = "SELECT COUNT(*) FROM " . static::getNomTable() . " WHERE id={$this->id} and {$nomId2}={$valeurId2} LIMIT 1";
        $result_set = $database->requete($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row) != 0;

    }

}



//    abstract public static function getListeChampsAffichage(); //todo : sortir les fonctions d'affichage de cette classe : interface Affichable et décorateur ?

//    public static function getListeChampsAffichage(): array
//    {
//        return array(
//            'login'=>['Identifiant','text'],
//            'hashedPassword'=>['Mot de passe','password']);
//    }

//    public static function getHTMLForm($listeChamps = ""): string
//    {
//        $res = "";
//        if ($listeChamps == "") $listeChamps = static::getListeChampsAffichage();
//
//        foreach ($listeChamps as $attribut => $valeur) {
//            $res .= static::creerEntreeHTML($attribut,$valeur[0],$valeur[1]);
//        }
//
//        return $res;
//
//    }
//
//
//
//    public static function creerEntreeHTML(string $nomAttribut, string $label, string $typeHTML): string
//    {
//        $input = "";
//        if (property_exists(static::class,$nomAttribut)) {
//            $label = sprintf('<label for="%1$s">%2$s</label>', $nomAttribut, $label);
//
//            $input = sprintf('<input name="%1$s" id="%1$s" type="%2$s" value="%3$s" />',
//                $nomAttribut, $typeHTML, $nomAttribut);
//        }
//        return $label . $input;
//    }


//public static function getListeChampsAffichage(): array
//     {
//         return array(
//             'login'=>['Identifiant','text'],
//             'hashedPassword'=>['Mot de passe','password']);
////             'prenom'=>'text',
////             'nom'=>'text',
////             'email'=>'email',
////             'droits'=>'number',
////             'droitsValides'=>'checkbox');
//
//     }