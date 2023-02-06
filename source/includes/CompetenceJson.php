<?php


class CompetenceJson
{
    public $type;
    public $name;
    public $depends;


    public function __construct()
    {
        $this->type = "";
        $this->name = "";
        $this->depends = array();
    }

}