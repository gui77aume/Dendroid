
<?php require_once("../includes/init.php");?>

<!--Template à inclure en bas de page-->

</section >
<br/>
<footer><div><p>CNAM - Systèmes d'information web - <?php echo date("Y", time()); ?>  - Guillaume Laisney</p></div><br>
<!--    <div><a href="--><?php //RACINE?><!--index.php">Dendroïde</a></div>-->
</footer>
</div>
</body>
</html>

<?php BaseDonnees::estInstanciee() ? BaseDonnees::getInstance()->fermerConnexion() : null;
?>