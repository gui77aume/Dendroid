<?php
//require_once(LIB_PATH . DS . 'BaseDonnees.php');

class Utilisateur extends ObjetMappe
{


    public $id;
    public $login;
    public $hashedPassword;
    public $prenom;
    public $nom;
    public $email;
    public $droits;
    public $droitsValides;
    public $emailVerifie;
    public $jeton;


    public function initJeton()
    {
        //boucle pour être certain d'avoir assez d'entropie pour random_bytes
        $this->jeton = -1;
        while ($this->jeton == -1) {
            $this->jeton = $this->genererJeton();
        }
    }

    private function genererJeton(): string
    {
        try {
            return bin2hex(random_bytes(50));
        } catch (Exception $e) {
            return '-1';
        }

    }

    public static function getNomsChampsBD(): array
    {
        return array(
            'id',
            'login',
            'hashedPassword',
            'prenom',
            'nom',
            'email',
            'emailVerifie',
            'jeton',
            'droits',
            'droitsValides');
    }

    public function envoyerMailVerif(): bool
    {


        $corps = '<!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <title>Message de vérification</title>
      <style>
        .wrapper {
            font-family: "Lato", Calibri, Arial, sans-serif;
            background: #ddd ;
            font-weight: 700;
            font-size: 16px;
            color: #333;
            -webkit-font-smoothing: antialiased;
            overflow-y: scroll;
            overflow-x: hidden;
        }
        
        a {
            display: inline-block;
            margin: 0 0.2em;
            padding: 3px;
            background: #97CAF2;
            border-radius: 2px;     
            -webkit-transition: all 0.3s ease-out;
            -moz-transition: all 0.3s ease-out;
            -o-transition: all 0.3s ease-out;
            transition: all 0.3s ease-out;
            text-decoration: none;
            font-weight: bold;
            color: white;
}

a:hover {
    background: #53a7ea
}

a:active {
    background: #c4e1f8
}

      </style>
    </head>

    <body>
      <div class="wrapper">
        <p>Bonjour, Merci de vous être inscrit à Dendroïde. Veuillez cliquer sur le lien ci-dessous pour valider votre compte :</p>
        <a href="' . RACINE . '/valideEmail.php?jeton=' . $this->jeton . '">Vérifier mon email</a>
      </div>
    </body>

    </html>';
        return envoyerMail($corps, "Dendroïde", $this->email, $this->nomComplet());
    }


    public function nomComplet(): string
    {
        if (isset($this->prenom) && isset($this->nom)) {
            return $this->prenom . " " . $this->nom;
        } else {
            return "";
        }
    }

    public function authentifier($password = ""): bool
    {
        // password_verify ( string $password , string $hash ) : bool
        $database = BaseDonnees::getInstance();


        $username = $database->echapper($this->login);

        $sql = 'SELECT hashedPassword FROM ' . Utilisateur::getNomTable();
        $sql .= " WHERE login = '{$username}' ";
        $sql .= 'LIMIT 1';
        $result_array = $database->requete($sql);
        $row = $database->fetch_array($result_array);
        if (empty($result_array)) return false;

        $hash = $row['hashedPassword'];


        return password_verify($password, $hash);
    }

    public static function getNomTable(): string
    {
        return "_utilisateur";
    }


    public static function indexAutoIncrement(): bool
    {
        return true;
    }
}

