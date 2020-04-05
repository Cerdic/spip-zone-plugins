<?php
session_start();//--On démarre la session
if(isset($_POST['char'])){$utf=trim($_POST['char']);}else{$utf='utf-8';}
header('Content-Type: text/html; charset='.$utf);
if(!empty($_POST['newsalon']))
{ require('../obj/salon.class.php');
	$newsalon = new salon("../db_catchat/",$utf);
		if($_POST['public']=="true")
		{$open='true';}
		else{$open='false';}
	$id=trim($_POST['catchatid']);
	$salon=htmlspecialchars(trim($_POST['newsalon']));
	$newsalon->execute('add','',array($salon,$open,$id)); //--On instancie l'objet d'édition et gestion du menu des salons
}?>