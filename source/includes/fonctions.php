<?php


use PHPMailer\PHPMailer\PHPMailer;


function redirigerVers($endroit = NULL)
{
    if ($endroit != NULL) {
        header("Location: {$endroit}");
        exit;
    }
}

function formaterMessage($message = ""): string
{

    if (!empty($message)) {
        return "<p class=\"message\">{$message}</p>";
    } else {
        return "";
    }
}

function __autoload($class_name)
{
    //$class_name = strtolower($class_name);
    $path = LIB_PATH . DS . "{$class_name}.php";
    if (file_exists($path)) {
        log_action("autoload a chargé une classe","Merci d'ajouter la classe $class_name à init.php"  );
        require_once($path);
    } else {
        log_action("__autoload de Fonctions.php","  {$class_name}.php n'a pu être trouvee.");
        die("__autoload de Fonctions.php :  {$class_name}.php n'a pu être trouvee.");
    }
}



function log_action($action, $message = "")
{
    $res = false;
    //$logfile = SITE_ROOT . DS . "log" . DS .'log.txt';
    $new = file_exists(LOGFILE) ? false : true;
    if ($handle = fopen(LOGFILE, 'a')) { // ajout
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "{$timestamp} | {$action}: {$message}\n";
        $res=fwrite($handle, $content);
        fclose($handle);
        if ($new) {
            chmod(LOGFILE, 0777);//TODO repasser en 0755 après tests
        }
    } else {
        echo "Fonctions.php : Impossible d'ouvrir le fichier de log pour écriture.";
    }
    return $res;
}


function creerFichier($cheminAbs, $content = "")
{
    $res = false;
//    $cheminAbs = SITE_ROOT . DS . $cheminRelatif;
    $new = file_exists($cheminAbs) ? false : true;
    if ($handle = fopen($cheminAbs, 'w')) { // écrasement
        $res=fwrite($handle, $content);
        fclose($handle);
        if ($new) {
            chmod($cheminAbs, 0777); //TODO repasser en 0755 après tests
        }
    } else {
        log_action("Fonctions.php"," Impossible d'ouvrir le fichier $cheminAbs pour écriture.");
    }
    return $res;
}


function test_input($data): string
{
    if(!isset($data) || empty($data)) return "";
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function contient($aiguille, $botteDeFoin): bool
{
  return  strpos($botteDeFoin, $aiguille) !== false;
//return (preg_match("/{$botteDeFoin}/i", $aiguille)) ;
}

function skip_accents( $str, $charset='utf-8' ) {

    $str = htmlentities( $str, ENT_NOQUOTES, $charset );

    $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
    $str = preg_replace( '#&([A-za-z]{2})(?:lig);#', '\1', $str );
    $str = preg_replace( '#&[^;]+;#', '', $str );

    return $str;
}


function envoyerMail($corps, $sujet, $addresseDest, $nomDest): bool
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet='UTF-8';
    $mail->ContentType='text/html';
    $mail->Host = MAILHOST;
    $mail->Port = MAILPORT;
    $mail->SMTPAuth = MAILSMTPAUTH;
    $mail->Username = MAILUSERNAME;
    $mail->Password = MAILPASSWORD;
    $mail->FromName = MAILFROMNAME;
    $mail->From = MAILFROM;
    try {
        $mail->AddAddress($addresseDest, $nomDest);
    } catch (\PHPMailer\PHPMailer\Exception $e) {
    }
    $mail->Subject = $sujet;
    $mail->Body = $corps;

//            <<<EMAILBODY
//
//Blabla
//
//EMAILBODY;

    try {
        return $mail->Send();
    } catch (\PHPMailer\PHPMailer\Exception $e) {
    }
    return false;

}
