<?php
/*
 * Plugin Articel Accueil
 * (c) 2011 Cedric Morin, Joseph
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des donnees du formulaire
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_article_accueil_charger($id_rubrique){
	$valeurs = array();
	$valeurs['id_rubrique'] = $id_rubrique;
	include_spip('base/abstract_sql');
	$valeurs['id_article_accueil'] = sql_getfetsel('id_article_accueil','spip_rubriques','id_rubrique='.intval($id_rubrique));
	return $valeurs;
}

/**
 * Traitement
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_article_accueil_traiter($id_rubrique){
	$update = array();
	if (!is_null($id_accueil=_request('id_article_accueil'))){
		include_spip('base/abstract_sql');
		$update['id_article_accueil'] = $id_accueil;
		sql_updateq('spip_rubriques',$update,'id_rubrique='.intval($id_rubrique));
	}
	
	return array('message_ok'=>'','editable'=>true);
}