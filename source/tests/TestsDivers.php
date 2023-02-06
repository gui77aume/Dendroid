<?php require_once("../includes/init.php");


use PHPUnit\Framework\TestCase;

class TestsDivers extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

    }

    public function testLog()
    {

        self::assertEquals(38,log_action("Test log"," OK ?"));

    }

    public function testMenuNav(){
        echo MenuNav::getMenuEnColonne();
    }

    public function testCreerFichier(){
$chemin = 'public/graph/data/default';
        self::assertEquals(6,creerFichier("$chemin./fichierTest.txt","test !"));
    }

}


