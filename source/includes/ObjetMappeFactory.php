<?php

require_once(LIB_PATH . DS . 'BaseDonnees.php');

/**
 * Class ObjetMappeFactory  pour instancier les objets de type ObjetBase à partir de la base de donnees.
 */
class ObjetMappeFactory
{


    public static function getTous($classeObjet): array
    {
        return self::getViaSql($classeObjet, "SELECT * FROM " . $classeObjet::getNomTable());
    }

    /**
     * @param $classeObjet
     * @param int $id
     * @return ObjetMappe
     * @throws Exception
     */

    public static function getViaId($classeObjet, $id = 0): ObjetMappe
    {
        $res = self::getViaSql($classeObjet, "SELECT * FROM " . $classeObjet::getNomTable() . " WHERE id={$id} LIMIT 1");
        if (empty($res)) throw new Exception("Objet de classe $classeObjet et d'identifiant {$id} non trouvé");

        return $res[0];
    }

    /**
     * @param $classeObjet
     * @param $nomChamp
     * @param $valeurChamp
     * @return ObjetMappe
     * @throws Exception
     */
    public static function getViaChamp($classeObjet, $nomChamp, $valeurChamp): ObjetMappe
    {
        $res = self::getViaSql($classeObjet, "SELECT * FROM " . $classeObjet::getNomTable() . " WHERE {$nomChamp}='{$valeurChamp}' LIMIT 1");
        if (empty($res)) throw new Exception("Objet de classe {$classeObjet}, dont le champ {$nomChamp} à pour valeur: {$valeurChamp} -- non trouvé.");

        return $res[0];

    }

    /**
     * @param $classeObjet
     * @param $nomChamp1
     * @param $valeurChamp1
     * @param $nomChamp2
     * @param $valeurChamp2
     * @return ObjetMappe
     * @throws Exception
     */
    public static function getViaDeuxChamps($classeObjet, $nomChamp1, $valeurChamp1, $nomChamp2, $valeurChamp2): ObjetMappe
    {
        $sql = "SELECT * FROM " . $classeObjet::getNomTable()
            ." WHERE {$nomChamp1}='{$valeurChamp1}' "
            ." AND {$nomChamp2}='{$valeurChamp2}' "
            ." LIMIT 1";
        $res = self::getViaSql($classeObjet, $sql );
        if (empty($res)) throw new Exception("Objet de classe {$classeObjet}, dont les champs {$nomChamp1} et {$nomChamp2} ont pour valeur: {$valeurChamp1} et {$valeurChamp2} -- non trouvé.");

        return $res[0];

    }

    /**
     * @param $classeObjet
     * @param $nomChamp
     * @param $valeurChamp
     * @return array
     * @throws Exception
     */
    public static function getMultipleViaChamp($classeObjet, $nomChamp, $valeurChamp): array
    {
        $res = self::getViaSql($classeObjet, "SELECT * FROM " . $classeObjet::getNomTable() . " WHERE {$nomChamp}='{$valeurChamp}'");
        if (empty($res)) throw new Exception("Objets de classe {$classeObjet}, dont le champ {$nomChamp} à pour valeur: {$valeurChamp} -- non trouvés.");

        return $res;

    }

    public static function getViaSql($classeObjet, $sql = ""): array
    {
        $database = BaseDonnees::getInstance();
        $result_set = $database->requete($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = new $classeObjet($row);
        }
        return $object_array;
    }


}