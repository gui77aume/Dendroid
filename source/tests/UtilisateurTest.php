
<?php require_once("../includes/init.php");


use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{

    protected function setUp(): void
{
    parent::setUp();

}

    public function testAuthentificationUniquementSiUtilisateurDansBase()
    {
        $this->effaceUtilSiExiste(3);
        $util = $this->creeInstanceUtilisateur(3);
        $this->assertEquals("$util->login", "pnom3");
        $this->assertFalse($util->authentifier('coucou'));
    }

    public function testAuthentificationAvecUtilisateurDansBase()
    {
        $this->effaceUtilSiExiste(3);
        $user = $this->creeInstanceUtilisateur(3);
        $this->assertEquals("$user->login", "pnom3");
        $nUsers = Utilisateur::nombreEntrees();
        $user->sauverDansBase();
        self::assertEquals($nUsers + 1, Utilisateur::nombreEntrees());
        $this->assertTrue($user->authentifier('coucou'));
    }

    public function testEnvoiMailConfirmation()
    {

        $this->effaceUtilSiExiste(3);
        $user = $this->creeInstanceUtilisateur(3);
        $user->sauverDansBase();
        $jeton = $user->jeton;

        $this->assertTrue($user->envoyerMailVerif());

        //echo __DIR__ ."/../public/admin/valideEmail.php?jeton=".$jeton;
    }



    private function effaceUtilSiExiste($num = '3')
    {
        try {
            $user = ObjetMappeFactory::getViaChamp(Utilisateur::class,'login', "pnom{$num}");
        } catch (Exception $e) {
//            $this->assertEquals("Objet de login de valeur pnom{$num} non trouvé", $e->getMessage());
        }
        if (!empty($user)) {
            $nUsers = Utilisateur::nombreEntrees();
            $user->effacerDeBase();
            self::assertEquals($nUsers - 1, Utilisateur::nombreEntrees());
        }

    }



    private function creeInstanceUtilisateur($num = '3'): Utilisateur
    {
        $util = new Utilisateur();
        $util->login = 'pnom' . $num;
        $util->hashedPassword = password_hash('coucou', PASSWORD_DEFAULT);
        $util->prenom = "Prénom" . $num;
        $util->nom = "Nom" . $num;
        //$util->email = 'prenom' . $num . '.nom' . $num . '@test.fr';
        $util->email = 'laisney.guillaume@gmail.com';
        $util->droits = '5';
        $util->droitsValides = true;
        $util->emailVerifie='0';
        $util->initJeton();
        return $util;
    }


}


//function creeUtilTest($num='3'){
//$util = new Utilisateur2();
//$util->login = 'pnom'.$num;
//$util->hashedPassword = password_hash('coucou', PASSWORD_DEFAULT);
//$util->prenom = "Prénom".$num;
//$util->nom = "Nom".$num;
//$util->email= 'prenom'.$num.'.nom'.$num.'@test.fr';
//$util->droits='5';
//$util->droitsValides=true;
//
//$util->save();
//
//}

//creeUtilTest(1);

//$util2 = new Utilisateur2();
//$util2->login="pnom";
//if($util2->authentifier('coucou') ) echo "OK" ; else echo "pas OK";
//if ($util2->authentifier('titi') ) echo "OK" ; else echo "pas OK";
//echo $util2->count_all();
//$test = Utilisateur2::class;
//$util3 = new $test();
//echo "<br/>\n";
//echo Utilisateur2::count_all();
//echo "\n<br/>\n";
//
//
//
//
//$fact = new DatabaseObjectFactory(Utilisateur2::class);
//foreach ($fact->find_all() as $utilisateur) {
//    var_dump($utilisateur);
//}
