<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Déclarer le traitement spécifique des textes d'articles
 * pour ajouter automatiquement la table des matières. 
 *
 * @param 
 * @return 
**/
function TableMatieres_declarer_tables_interfaces($interface){
	include_spip('table_matieres');

	// ne retourner que la table des matieres du texte fourni (champ texte)
	$interface['table_des_traitements']['TABLE_MATIERES'] = 'table_matieres(%s, \'tdm\')';

	// traiter les articles si le sommaire automatique est actif
	if (_AUTO_ANCRE == 'oui') {
		$traitements_actuels =
			isset($interface['table_des_traitements']['TEXTE']['articles'])
				? $interface['table_des_traitements']['TEXTE']['articles']
				: $interface['table_des_traitements']['TEXTE'][0];
				
		// completer les traitements actuels, mais le sommaire automatique passe en preum's
		$interface['table_des_traitements']['TEXTE']['articles'] =
			str_replace('%s', 'table_matieres(%s)', $traitements_actuels);
	}
	
	return $interface;
}

?>
