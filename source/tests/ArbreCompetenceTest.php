<?php require_once("../includes/init.php");


use PHPUnit\Framework\TestCase;

class ArbreCompetenceTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

    }

    public function testCreation()
    {
        ArbreCompetence::genererArbreJson();
    }



}


