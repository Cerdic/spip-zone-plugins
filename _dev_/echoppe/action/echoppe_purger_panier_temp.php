<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_purger_panier_temp(){

	$sql_purger_panier_temp = "DELETE FROM spip_echoppe_paniers WHERE statut='temp';";
	$res_purger_panier_temp = spip_query($sql_purger_panier_temp);
	redirige_par_entete(generer_url_ecrire('echoppe_paniers'));
	
}

?>
