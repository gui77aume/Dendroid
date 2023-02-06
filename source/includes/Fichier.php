<?php

class Fichier extends ObjetMappe
{

    public $id;
    public $nom;
    public $type;
    public $taille;
    public $donnees;
    public $id__ressource;






    public static function getNomTable(): string
    {
        return '_fichier';
    }

    public static function indexAutoIncrement(): bool
    {
        return true;
    }

    public static function getNomsChampsBD(): array
    {
        return array(
            'id',
            'id__ressource',
            'nom',
            'type',
            'taille',
            'donnees');
    }


    public static function traductionErreurs($erreurNum): string
    {
        $errs= array(
            // http://www.php.net/manual/fr/features.file-upload.errors.php
            UPLOAD_ERR_OK 				=> "Pas d'erreur.",
            UPLOAD_ERR_INI_SIZE  	=> "Fichier plus grand que la limite stipulée dans upload_max_filesize.",
            UPLOAD_ERR_FORM_SIZE 	=> "Fichier plus grand que la limite stipulée par le formulaire d'envoi.",
            UPLOAD_ERR_PARTIAL 		=> "Le fichier n'a été que partiellement téléchargé.",
            UPLOAD_ERR_NO_FILE 		=> "Aucun fichier n'a été téléchargé.",
            UPLOAD_ERR_NO_TMP_DIR => " Un dossier temporaire est manquant. ",
            UPLOAD_ERR_CANT_WRITE => "Impossible d'écrire sur le disque.",
            UPLOAD_ERR_EXTENSION 	=> "Une extension PHP a arrêté l'envoi de fichier..."
        );

        return $errs[$erreurNum];


    }

//        `id` int(11) NOT NULL AUTO_INCREMENT,
//  `nom` varchar(100) NOT NULL,
//  `type` varchar(30) NOT NULL,
//  `taille` int(11) NOT NULL,
//  `donnees` longblob NOT NULL,
//  PRIMARY KEY (`id`)

}