<?php

/**
 * Class FichierSansDonnees
 * A utiliser  quand on n'a pas besoin d'acceder aux données du fichier, ex pour afficher son nom sans tout mettre en mémoire.
 */

class FichierSansDonnees extends Fichier
{

    public $id;
    public $nom;
    public $type;
    public $taille;
    //public $donnees;
    public $id__ressource;



    public static function getNomsChampsBD(): array
    {
        return array(
            'id',
            'id__ressource',
            'nom',
            'type',
            'taille',
            );
    }



}