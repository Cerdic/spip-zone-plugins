<?php
/**
 * Plugin Manuel du site
 *
 * Formulaire de configuration du plugin
 * 
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function construire_lien($t){
	if (version_compare($GLOBALS['spip_version_branche'], '3.O', '>=')){
		$table = table_objet_sql($t);
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table);		
	}else{
		$desc['url_edit'] = concat($t,'_edit');
		
	}
	$url = parametre_url(generer_url_ecrire($desc['url_edit']),'new','oui');
	
	return $url;
}

?>