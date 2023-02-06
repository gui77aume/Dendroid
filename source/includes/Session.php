<?php


class Session {
	
	private $logged_in=false;
	public $user_id;
	public $message;

    private static $_instance;

    public static function getInstance(): Session
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

	//TODO utiliser un singleton plutôt qu'un var globale...
	function __construct() {
		@session_start();
		$this->check_message();
		$this->check_login();
    if($this->logged_in) {
      //todo
    } else {

    }
	}
	
  public function is_logged_in(): bool
  {
    return $this->logged_in;
  }

	public function login(Utilisateur  $user) {
    if($user){
      $this->user_id = $_SESSION['user_id'] = $user->id;
      $this->logged_in = true;
    }
  }
  
  public function logout() {
    unset($_SESSION['user_id']);
    unset($this->user_id);
    $this->logged_in = false;
  }

	public function message($msg=""): string
    {
	  if(!empty($msg)) {
	    $_SESSION['message'] = $msg;
	    return "";
	  } else {
			return $this->message;
	  }
	}

	private function check_login() {
    if(isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->logged_in = true;
    } else {
      unset($this->user_id);
      $this->logged_in = false;
    }
  }
  
	private function check_message() {

		if(isset($_SESSION['message'])) {

      $this->message = $_SESSION['message'];
      unset($_SESSION['message']);
    } else {
      $this->message = "";
    }
	}
	
}

//$session = new Session();
//$message = $session->message();

