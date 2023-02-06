<?php


defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

///home/laisney/Dropbox/CNAM/siw-NFE114/apache/sip
defined('SITE_ROOT') ? null :
    define('SITE_ROOT', DS . 'home' . DS . 'laisney' . DS . 'Dropbox' . DS . 'CNAM' . DS . 'siw-NFE114' . DS . 'apache' . DS . 'sip');

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT . DS . 'includes');

require_once(LIB_PATH . DS . 'configGenerale.php');
require_once(LIB_PATH . DS . 'configBaseDonnees.php');
require_once(LIB_PATH . DS . 'configSMTP.php');

require_once(LIB_PATH . DS . 'PHPMailer' . DS . 'Exception.php');
require_once(LIB_PATH . DS . 'PHPMailer' . DS . 'SMTP.php');
require_once(LIB_PATH . DS . 'PHPMailer' . DS . "phpmailer.lang-fr.php");
require_once(LIB_PATH . DS . 'PHPMailer' . DS . 'PHPMailer.php');



require_once(LIB_PATH . DS . 'fonctions.php');

// Objets fondamentaux
require_once(LIB_PATH . DS . 'Session.php');
require_once(LIB_PATH . DS . 'BaseDonnees.php');
require_once(LIB_PATH . DS . 'ObjetMappe.php');
require_once(LIB_PATH . DS . 'ObjetMappeFactory.php');
require_once(LIB_PATH . DS . 'Pagination.php');
require_once(LIB_PATH . DS . 'RelationBinaire.php');

// Objets à manipuler, relatifs à la base
require_once(LIB_PATH . DS . 'Utilisateur.php');
require_once(LIB_PATH . DS . 'Fichier.php');
require_once(LIB_PATH . DS . 'FichierSansDonnees.php');
require_once(LIB_PATH . DS . 'Competence.php');
require_once(LIB_PATH . DS . 'CompetenceJson.php');
require_once(LIB_PATH . DS . 'Concerner.php');
require_once(LIB_PATH . DS . 'MenuNav.php');
require_once(LIB_PATH . DS . 'Requerir.php');
require_once(LIB_PATH . DS . 'Ressource.php');


