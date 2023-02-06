<?php
require_once(LIB_PATH . DS . "configBaseDonnees.php");

class BaseDonnees {

	private $connexion;
	public $derniereRequete;
	private $magicQuotesActive;

    private static $_instance;

    public static function getInstance(): BaseDonnees
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function estInstanciee(): bool
    {
        if (self::$_instance) {
            return true;
        }
        return false;
    }


  private function __construct() {
    $this->ouvrirConnexion();
    $this->magicQuotesActive = get_magic_quotes_gpc();
  }

	public function ouvrirConnexion() {
        $this->connexion = new mysqli(DB_SERVER, DB_USER,DB_PASS,DB_NAME);
		if ($this->connexion->connect_error) {
            log_action("BaseDonnees->ouvrirConnexion Erreur fatale :","Echec de connexion à la base de donnees : " . $this->connexion->connect_error);
			die("Echec de connexion à la base de donnees : " . $this->connexion->connect_error);
		}

	}

	public  function fermerConnexion() {
		if(isset($this->connexion)) {
            $this->connexion->close();
			unset($this->connexion);
		}
	}

	public function requete($sql) {
      // echo "\n<br/>". var_dump($sql). "<br/>\n";
		$this->derniereRequete = $sql;
        $result=$this->connexion->query($sql);
		$this->confirmerRequete($result);
		return $result;
	}
	
	public function echapper($valeur ) {
			if( $this->magicQuotesActive ) { $valeur = stripslashes( $valeur ); }
			return  $this->connexion->real_escape_string( $valeur );
	}
	
	//methodes spécifiques MySQL
  public function fetch_array($result_set): ?array
  {
    return mysqli_fetch_array($result_set);
  }
  
  public function num_rows($result_set): int
  {
   return mysqli_num_rows($result_set);
  }

    /**
     * @return int|string identifiant de la derniere insertion
     */
  public function insertId()  {
    return mysqli_insert_id($this->connexion);
  }

    /**
     * @return int nb de rang affectes par la requete
     */
  public function nRangsAffectes(): int
  {
    return mysqli_affected_rows($this->connexion);
  }

	private function confirmerRequete($result) {
		if (!$result) {
	    $output = "Echec de la requete : " . mysqli_error($this->connexion) . "<br /><br />";
	     $output .= "Derniere requete : " . $this->derniereRequete;
	    //TODO logger proprement et pê gérer avec exceptions plutot que die ?
            {
                log_action("BaseDonnees->confirmerRequete Erreur fatale :",$output);
               // if(DEBUG) die($output); else die("erreur SQL");

            }}
	}
	
}


