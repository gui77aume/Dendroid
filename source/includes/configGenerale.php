<?php

defined('DEBUG') ? null : define("DEBUG", true);

defined('RACINE') ? null : define("RACINE",'http://'.'guillaume.ooguy.com');
//defined('RACINE') ? null : define("RACINE",'http://'.$_SERVER["SERVER_NAME"]); à utiliser avec prudence...
//defined('RACINE') ? null : define("RACINE",'http://'.'localhost'); //pour les tests

defined('ITEMSPARPAGE') ? null : define("ITEMSPARPAGE", 10); //pour l'affichage des listes en attendant paramétrage utilisateur

defined('LOGFILE') ? null : define("LOGFILE",SITE_ROOT . DS . "log" . DS .'log.txt');

defined('REPGRAPHDATA') ? null : define("REPGRAPHDATA",SITE_ROOT . DS . 'public/graph/data/default');

//Limite pour les formulaires d'envoi.
// Penser à ajuster la limite serveur ds php.ini
// Attention aussi à condifgurer max_allowed_packet dans MariaDB (16Mio par def depuis MariaDB 10.2.4)
defined('MAX_ENVOI_FICHIER_FORM') ? null : define("MAX_ENVOI_FICHIER_FORM",2000000);


