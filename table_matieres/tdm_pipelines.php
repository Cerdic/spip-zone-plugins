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
	$interface['table_des_traitements']['TEXTE']['articles'] =
		str_replace(
			'%s',
			'TableMatieres_LienRetour(TableMatieres_AjouterAncres(%s))',
			isset($interface['table_des_traitements']['TEXTE']['articles'])
				? $interface['table_des_traitements']['TEXTE']['articles']
				: $interface['table_des_traitements']['TEXTE'][0]
		);
	$interface['table_des_traitements']['TABLE_MATIERES']['articles']= 'TableMatieres_LienRetour(TableMatieres_AjouterAncres(%s), true)';
	return $interface;
}

?>
