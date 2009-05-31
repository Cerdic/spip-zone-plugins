<?php
/*
 * Boucle xml
 * 
 *
 * Auteur :
 * Cedric Morin
 * (c) 2006 - Distribue sous licence GNU/GPL
 *
 */

function xml_fill_table_temporaire_boucle($xml_file, $is_code=false){
	include_spip('base/xml_temporaire');
	xml_creer_tables_temporaires();
	if (($xml_file{0}=='"')||($xml_file{0}=="'"))
		$xml_file = substr($xml_file,1,strlen($xml_file)-2);
	xml_fill_table($xml_file, $is_code);
}
?>