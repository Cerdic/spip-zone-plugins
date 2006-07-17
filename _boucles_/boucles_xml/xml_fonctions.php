<?php
/*
 * Boucle xml
 * 
 *
 * Auteur :
 * Cedric Morin
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */
include_spip('base/xml_temporaire');
function boucle_XML_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_xml";
	$boucle->select[] =  $boucle->id_table.".xpath";
	$boucle->hash = '
	// CREER la table temporaire xml et la peupler avec le resultat du parser
	xml_fill_table_temporaire_boucle(spip_abstract_quote($Pile[$SP]["xml"]));
';
	return calculer_boucle($id_boucle, $boucles); 
}

?>