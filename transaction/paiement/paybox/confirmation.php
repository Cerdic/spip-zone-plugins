<?php

	include_once("../../../classes/Commande.class.php");
	include_once("../../../fonctions/divers.php");
		
	$commande = new Commande();

	$commande->charger_trans($_REQUEST['ref']);
	if($_REQUEST['erreur']=="00000" && $_REQUEST['auto']!="XXXXXX"){
	 $commande->statut = 2;
	 $commande->genfact();
	}
	
	
	$commande->maj();

	modules_fonction("confirmation", $commande);

?>