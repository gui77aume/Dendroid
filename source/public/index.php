<?php require_once("../includes/init.php"); ?>
<?php
$session = Session::getInstance();
if (!$session->is_logged_in()) { redirigerVers("connexion.php"); } ?>

<?php include ('entete.php'); ?>

<?php echo formaterMessage($session->message); ?>




<h2>Accueil - informations</h2>
<br>


   <p> L'objectif de cette application est d'aider à organiser et à mutualiser des ressources pédagogiques.
    L'organisation de compétences en graphe a pour but de fournir une aide à la création de parcours et de séquences de formation interdisciplinaires.</p>

<br>
<p>Les ressources (fichiers ou liens) sont rendues accessibles par visualisation  d'un graphique interactif du graphe de compétences ou par des listes filtrées des compétences et des ressources.<br/>
    Le filtrage se fait par "et" logique sur les termes saisis du titre et de la description</p>

<br/>
<p>La sélection d&#39;une compétence permet d&#39;obtenir des liens vers les ressources disponibles, ses pré-requis et vers les autres compétences qui y font appel.</p>
<br>
<p>Des informations sont annexées à chaque ressource : description,  mots clefs.</p>
<br>
<p>Cette application est en cours de construction, elle est conçue pour fonctionner avec Firefox et Chrome.</p>

<?php include('piedPage.php'); ?>
