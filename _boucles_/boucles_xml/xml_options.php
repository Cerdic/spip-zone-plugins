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

function xml_fill_table_temporaire_boucle($xml_file){
	include_spip('base/xml_temporaire');
	xml_creer_tables_temporaires();
	xml_fill_table($xml_file);
}

?>