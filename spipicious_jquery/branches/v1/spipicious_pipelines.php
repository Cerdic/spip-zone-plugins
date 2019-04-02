<?php
/**
 * SPIP.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational (http://www.erational.org)
 *
 * © 2007-2013 - Distribue sous licence GNU/GPL
 * 
 * Fichiers des pipelines du plugin
 * 
 * @package SPIP\SPIPicious\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * 
 * Insertion du code javascript nécessaire dans le head
 * Pour ce faire, on utilise la fonction dédiée dans le plugin sélecteur générique
 * 
 * @param string $flux
 * 		Le contenu de la balise #INSERT_HEAD
 * @return string $flux
 * 		Le contenu de la balise #INSERT_HEAD modifié
 */
function spipicious_insert_head($flux){
	include_spip('inc/autoriser');
	if(autoriser('tagger_spipicious')){
		include_spip('selecteurgenerique_fonctions');
		$flux .= selecteurgenerique_verifier_js($flux);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline optimiser_base_disparus (SPIP)
 * 
 * Supprimer les liens spipicious/objet sur les éléments disparus
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du flux non modifié
 */
function spipicious_optimiser_base_disparus($flux){
	/**
	 * On fonctionne comme les documents dans genie/optimiser
	 */
	$r = sql_select("DISTINCT objet","spip_spipicious");
	while ($t = sql_fetch($r)){
		$type = $t['objet'];
		$spip_table_objet = table_objet_sql($type);
		$id_table_objet = id_table_objet($type);
		$res = sql_select("L.id_mot AS id,L.id_objet AS id_objet",
			      "spip_spipicious AS L
			        LEFT JOIN $spip_table_objet AS O
			          ON O.$id_table_objet=L.id_objet AND L.objet=".sql_quote($type),
				"O.$id_table_objet IS NULL");
		while ($row = sql_fetch($res)){
			sql_delete("spip_spipicious", array("id_mot=".$row['id'],"id_objet=".$row['id_objet'],"objet=".sql_quote($type)));
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_boucle (SPIP)
 * 
 * Dans une boucle SPIPICIOUS, si on ne passe pas le critère tout ou statut, 
 * on n'affiche pas ceux non publiés
 * 
 * @param object $boucle
 * 		Les paramètres de la boucle
 * @return object $boucle
 * 		Les paramètres de la boucle modifiés
 */
function spipicious_pre_boucle($boucle){
	if ($boucle->type_requete == 'spipicious') {
		$id_table = $boucle->id_table;
		$mstatut = $id_table .'.statut';
		if (!isset($boucle->modificateur['criteres']['tout'])
			&& !isset($boucle->modificateur['criteres']['statut']))
			$boucle->where[]= array("'='", "'$mstatut'", "'\"publie\"'");
	}
	return $boucle;
}

/**
 * Insertion dans le pipeline de post-edition (SPIP)
 * 
 * A l'institution d'un objet (changement de statut), 
 * si l'objet n'a pas le statut publié et qu'il a des tags spipicious, on passe ses tags en prop
 * sinon, on les publie
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return $flux
 * 		Le contexte du pipeline sans modification
 */
function spipicious_post_edition($flux){
	if($flux['args']['action'] == 'instituer'){
		$objet = objet_type($flux['args']['table']);
		if(isset($flux['data']['statut']) && ($flux['data']['statut'] != 'publie'))
			sql_updateq('spip_spipicious',array('statut'=>'prop'),'id_objet='.intval($flux['args']['id_objet']).' AND objet='.sql_quote($objet).' AND statut="publie"');
		else
			sql_updateq('spip_spipicious',array('statut'=>'publie'),'id_objet='.intval($flux['args']['id_objet']).' AND objet='.sql_quote($objet).' AND statut="prop"');
	}
	return $flux;
}
?>