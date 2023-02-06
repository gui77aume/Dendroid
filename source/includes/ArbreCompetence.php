<?php


class ArbreCompetence
{


    public static function genererArbreJson()
    {

        //TODO : génération dans deux reps alternatifs
        //TODO : génération de config.json avec taille proportionnelle à la surface occupée par le disque de compétences.
        $baseCompetences = ObjetMappeFactory::getTous("Competence");
        self::effacerFichiers();
        $res = array();

        foreach ($baseCompetences as $competence) {
            self::genererMkDwn($competence);
            $compJson = new CompetenceJson();
            $compJson->type = "domPro";
            $compJson->name = $competence->nomCourt;
            $preRequis = $competence->getPreRequis();
            foreach ($preRequis as $pr) {
                $compJson->depends[] = $pr->nomCourt;
            }
            $res[] = $compJson;
        }

        $arbreJSON = json_encode($res);
        $fichierObj = REPGRAPHDATA . "/objects.json";
        creerFichier($fichierObj, $arbreJSON);
    }

    private static function genererMkDwn($competence)
    {
        $cible = "../competenceDetail.php?id=" . $competence->id;
        $fichierComp = REPGRAPHDATA . DS . $competence->nomCourt . ".mkdn";

        $contenu = $competence->description;

        $contenu .= "<br/><a href=\"$cible\">Détails de la compétence.</a>";
        $contenu .= "<br/>";

        $ressources = Concerner::getRessourcesAssociees($competence->id);
        if (!empty($ressources)) {
        sizeof($ressources)==1?$s="":$s="s";
        $contenu .= "Ressource{$s} associée{$s}:";
            foreach ($ressources as $ressource) {
                $contenu .= "<br/><a href=\"";
                $contenu .= "../ressourceDetail.php?id=" . $ressource->id;
                $contenu .= "\">" . $ressource->titre . "</a>";
            }
        }

        creerFichier($fichierComp, $contenu);
    }

    private static function effacerFichiers()
    {
        try {
            $fichiersMkdn = REPGRAPHDATA . "/*.mkdn";
            array_map('unlink', glob($fichiersMkdn));
        } catch (Exception $ignored) {
        }

        try {
            $fichierObjets = REPGRAPHDATA . '/objects.json';
            unlink($fichierObjets);
        } catch (Exception $ignored) {
        }

    }


}