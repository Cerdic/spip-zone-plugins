<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Déclarer le traitement spécifique des textes d'articles
 * pour ajouter automatiquement la table des matières. 
 *
 * @param array $interface : le tableau des interfaces
 * @return array $interface : le tableau avec les champs modifiés 
 */
function tablematieres_declarer_tables_interfaces($interface){
	include_spip('tablematieres_fonctions');

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

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * 
 * Ajout du fichier javascript du plugin
 * 
 * @param array $plugins
 * 		Le tableau des js déjà insérés
 * @return array $plugins
 * 		Le tableau des js complété 
 */
function tablematieres_jquery_plugins($plugins){
	$plugins[] = 'javascript/table_matieres.js';
	return $plugins;
}

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * 
 * @param string $flux
 * 		Le contenu de la balise #INSERT_HEAD
 * @return string $flux
 * 		Le contenu de la balise #INSERT_HEAD complétée 		
 */
function tablematieres_insert_head($flux){
	include_spip('inc/config');
	$flux .= "<script type='text/javascript'>/* <![CDATA[ */
var tdm_retour = '".preg_replace(
		',<img,i',
		'<img alt="' . _T('tdm:retour_table_matiere')
		.'" title="' . _T('tdm:retour_table_matiere') . '"',
		_RETOUR_TDM)."';
var tdm_flottante = ".((lire_config('table_matieres/tdm_flottante','off') == 'on') ? 'true': 'false').";
/* ]]> */</script>";
	return $flux;
}
?>
