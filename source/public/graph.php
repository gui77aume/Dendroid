

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Système de gestion de ressources pédagogiques</title>
    <link href="css/main.css" media="all" rel="stylesheet" type="text/css"/>

</head>
<body>
<div class="container">
    <header>
        <h1><strong>Dendroïde</strong></h1>
        <h2>Ingénierie pédagogique</h2>

    </header>
    <menu><ul ><li  style="display: inline" ><a href="index.php">Accueil</a></li><li  style="display: inline"><a href="graph/graph.php">Graphe des compétences</a></li><a href="competencesListe.php">Liste des compétences</a><a href="ressourcesListe.php">Liste des ressources</a><a href="logout.php">Déconnexion</a></ul></menu>    <br/>

    <section class="main">

        <h2>Modification d'une compétence</h2>
        <br/>

        <script>
            function modif(){
                const divlien = document.getElementById("ajouter");
                console.log("modif");
                divlien.innerHTML="Les modifications doivent être enregistréee avant d'ajouter un pré-requis "
            }
        </script>

        <div class="entiteCadree clearfix">
            <form action="competenceAjoutModif.php" method="get">
                <h3><label>Nom : <input type="text" name="nom" value="Tracer la médiatrice d’un segment"/></label></h3>
                <div class="hautEntite">
                    <label>Nom court: <input type="text" name="nomCourt" value="Mediatrice" onchange="modif()" onkeyup="modif()"/></label>
                    <br/>
                    <br/>
                    <label>Description : <textarea name="description" rows="5" cols="70" onchange="modif()" onkeyup="modif()">Tracer la médiatrice d’un segment aux instruments puis à l'aide d'un outil numérique.
La bissectrice est un pré-requis éventuel car la médiatrice est la bissectrice d'un angle plat.</textarea>
                    </label>
                </div>

                <input type="hidden" name="id" value="60">

                <div class="hautEntite"> <p>Liste des pré-requis :</p>
                    <div style="display: table-row">
                        <div style="display:table-cell">
                            <a href="competenceDetail.php?id=108">Bissectrice d'un angle</a>
                        </div>
                        <div style="display:table-cell">
                            <a style="display: table-cell"
                               href="competenceAjoutSupprPreRequis.php?idCible=60&idPreRequis=108&type=suppression">Supprimer
                                ce pré-requis</a>
                        </div>
                    </div>
                </div>
                <div class="hautEntite" id="ajouter"><a href="competencesListe.php?idCompCible=60" >Ajouter un pré-requis</a></div>
                <div class="basEntite">
                    <div><input type="submit" name="submit" value="Enregistrer" class="boutonVert"/></div>
                </div>

            </form>

        </div>




        <!--Template à inclure en bas de page-->

    </section >
    <br/>
    <footer><div><p>CNAM - Systèmes d'information web - 2021  - Guillaume Laisney</p></div><br>
        <!--    <div><a href="--><!--index.php">Dendroïde</a></div>-->
    </footer>
</div>
</body>
</html>