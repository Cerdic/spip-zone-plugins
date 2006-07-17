<?php

function boucle_FORMS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_xml";
	$boucle->hash = '
	// CREER les table temporaire forms_champs et forms_champs_choix
	xml_fill_table_temporaire_boucle();
';
	return calculer_boucle($id_boucle, $boucles); 
}

?>