<?php


class MenuNav
{
    private static $itemsLogge;
    private static $itemsPublics;

    private static function init()
    {
        self::$itemsLogge = array();
        self::$itemsPublics =array();

        self::$itemsPublics[]=  '<a href="index.php">Accueil</a>';

        self::$itemsLogge[] = '<a href="graph/graph.php">Graphe des compétences</a>';
        self::$itemsLogge[] = '<a href="competencesListe.php">Liste des compétences</a>';
        self::$itemsLogge[] = '<a href="ressourcesListe.php">Liste des ressources</a>';
        self::$itemsLogge[] = '<a href="logout.php">Déconnexion</a>';


    }

    public static function getMenuArray($logged): array
    {
        self::init();
        $logged? $res =  array_merge(self::$itemsPublics,self::$itemsLogge):$res = self::$itemsPublics ;
        return $res;
    }

    public static function getMenuEnLigne($logged=false): string
    {
        //<ul ><li  style="display: inline" >
        $res="<ul>";
        self::init();
        foreach (self::getMenuArray($logged) as $item){
            $res .= '<li  style="display: inline" >'. $item . '</li>';
        }
        $res .= "</ul>";
        return $res;
    }

    public static function getMenuEnColonne($logged=false): string
    {
        $res="<ul>";
        self::init();
        foreach (self::getMenuArray($logged) as $item){
            $res .= "<li>".$item."</li>";
        }
        $res .= '</ul>';

        return $res;

    }




}