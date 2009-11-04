<?php
/**
 * 
 * Trad-lang v1
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil
 * 
 */

/**
 * Ajout d'un bouton dans la barre d'onglet de configuration des langues
 * 
 * @param object $flux
 * @return 
 */
function tradlang_ajouter_onglets($flux) {
	if($flux['args']=='config_lang')
		$flux['data']['tradlang'] = new Bouton( 
			"traductions-24.gif", _L('tradlang:gestion_des_traductions'),
			generer_url_ecrire("tradlang"));
	return $flux;
}

/**
 * Insertion dans le pipeline declarer_tables_objets_surnoms (base/connect_sql.php)
 * La table spip_tradlang est une table ancienne, et n'a pas de S final ...
 * Pour éviter les problèmes liés à cela, on surnomme les objets
 * 
 * @param array $flux La liste des surnoms
 * @return array Le $flux complété
 */
function tradlang_declarer_tables_objets_surnoms($flux){
	$flux['tradlang'] = 'tradlang';
	return $flux;
}

/**
 * Insertion dans le pipeline gouverneur_infos_tables_versions
 * (utile pour le plugin revisions en 2.1)
 * Permet de gérer les révisions sur les chaines de langue
 *
 * @param object $array
 * @return
 */
function tradlang_gouverneur_infos_tables($array){
	$array['spip_tradlang'] = array(
								'table_objet' => 'tradlang',
								'type' => 'tradlang',
								'url_voir' => '',
								'texte_retour' => '',
								'url_edit' => '',
								'texte_modifier' => '',
								'icone_objet' => 'spip_lang',
								'texte_unique' => 'tradlang:chaine_langue',
								'texte_multiple' => 'tradlang:chaines_langue',
								'champs_versionnes' => array('str','comm', 'status')
							);
	return $array;
}

/**
 * Insertion dans le pipeline revisions_liste_objets du plugin revisions (2.1)
 * Definir la liste des tables possibles
 * @param object $array
 * @return
 */
function tradlang_revisions_liste_objets($array){
	$array['tradlang'] = 'tradlang:chaines_langue';
	return $array;
}
?>