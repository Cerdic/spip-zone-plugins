<?php
/**
 * Plugin  : Étiquettes
 * Auteur  : RastaPopoulos
 * Licence : GPL
 *
 * Documentation : https://contrib.spip.net/Plugin-Etiquettes
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_SELECTEUR_GENERIQUE_ACTIVER_PUBLIC')) 
		define('_SELECTEUR_GENERIQUE_ACTIVER_PUBLIC', true);


function etiquettes_produire_id($groupe, $type_objet='', $id_objet=''){

	$elements = compact("groupe", "type_objet", "id_objet");
	$operations = function($e) {
		return str_replace(
			" ",
			"_",
			preg_replace(
				",([^[:cntrl:][:alnum:]_]|[[:space:]])+,u",
				" ",
				translitteration(
					corriger_caracteres(
						strtolower(
							supprimer_tags(
								supprimer_numero($e)
							)
						)
					)
				)
			)
		);
	};
	
	$elements = array_map($operations, $elements);
	return trim(join('_', $elements), '_');

}

function valeur_champ_tags($table, $id, $champ) {
	
	include_spip('base/objets');
	$r = spip_query('SELECT ALL titre FROM spip_mots AS m RIGHT JOIN spip_mots_liens AS j ON m.id_mot=j.id_mot WHERE j.objet='.sql_quote(objet_type($table)).' AND j.id_objet='.intval($id));
	$liste = array();
	while($a = spip_fetch_array($r)){
		array_push($liste,$a['titre']);
	}
	return empty($liste) ? "drfhdtrhrtfgh" : join(', ', $liste);
	
}

function tags_revision($id_objet, $colonnes, $type_objet){

	// Pour l'instant on ne fait rien ! On essaye pas de mettre à jour
	// automatiquement, on fait ça à la main dans la vue.
	// return;
	
	// S'il n'y a rien a modifier...
	if (!isset($colonnes['tags'])) return false;
	
	// On va chercher la bonne table et clé
	include_spip('base/objets');

	// On met à jour les tags
	include_spip('inc/tag-machine');
	ajouter_mots($colonnes['tags'], $id_objet, 'tags', table_objet($type_objet), id_table_objet($type_objet), true);
	return true;

}

?>
