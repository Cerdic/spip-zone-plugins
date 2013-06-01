<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010-2013 - Distribue sous licence GNU/GPL
 *
 * Action d'édition d'un diogene
 *
 **/
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_diogene_dist($arg=null){
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_diogene n'est pas un nombre, c'est une creation
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_diogene = intval($arg)) {
		if (!$GLOBALS['visiteur_session']['id_auteur']) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_diogene = diogene_inserer();
	}

	// Enregistre l'envoi dans la BD
	if ($id_diogene > 0)
		$err = diogene_modifier($id_diogene);
	
	if ($err)
		spip_log("echec editeur diogene: $err",_LOG_ERREUR);
	
	return array($id_diogene,$err);
}

/**
 * Fonction de changement de statut d'un template
 *
 * @param unknown_type $id_diogene
 * @param unknown_type $c
 */
function diogene_instituer($id_diogene, $c) {

	// Envoyer aux plugins
	$c = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_diogenes',
				'id_objet' => $id_diogene,
				'action'=>'instituer'
			),
			'data' => $c
		)
	);

	if (!count($c)) return;

	// Envoyer les modifs.

	sql_updateq('spip_diogenes', $c, "id_diogene=$id_diogene");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_diogenes',
				'id_objet' => $id_diogene,
				'action'=>'instituer'
			),
			'data' => $c
		)
	);

	return ''; // pas d'erreur
}

/**
 * Fonction d'insertion d'un template
 * 
 * @return int $id_diogene
 * 		L'identifiant numérique du template créé
 */
function diogene_inserer() {

	$champs = array();
	$champs['id_auteur'] = $GLOBALS['visiteur_session']['id_auteur'];
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_diogenes',
			),
			'data' => $champs
		)
	);
	$id_diogene = sql_insertq("spip_diogenes", $champs);

	return $id_diogene;
}

/**
 * Fonction de suppression d'un diogene
 * 
 * @param int $id_diogene
 * 		L'identifiant numérique du diogène à supprimer
 * @return bool true/false
 */
function diogene_supprimer($id_diogene){
	$diogene = sql_fetsel('*','spip_diogenes','id_diogene='.intval($id_diogene));
	if(include_spip('inc/autoriser') && $diogene && autoriser('modifier','diogene',$id_diogene)){
		if($del = sql_delete('spip_diogenes','id_diogene = '.intval($id_diogene))){
			/**
			 * Invalider le cache
			 */
			include_spip('inc/invalideur');
			$invalideur = "id='diogene/$id_diogene'";
			suivre_invalideur("$invalideur");
			return true;
		}else
			return false;
	}else{
		return false;
	}
}

/**
 * Fonction de révision d'un diogène
 * 
 * @param int $id_diogene Identifiant numérique du diogene
 * @param array $champs un tableau des champs à modifier en base
 */
function diogene_modifier($id_diogene,$set=false){
	
	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		objet_info('diogene','champs_editables'),
		// black list
		array('id_secteur','objet','type'),
		// donnees eventuellement fournies
		$set
	);
	
	if(!_request('menu')){
		$c['menu'] = '';
	}
	/**
	 * Les champs champs_caches, champs_ajoutes, options_complements
	 * doivent être des tableau serialisés
	 */
	foreach(array(
		'champs_caches','champs_ajoutes','options_complements'
	) as $champ){
		if(isset($c[$champ]) && is_array($c[$champ]))
			$c[$champ] = serialize($c[$champ]);
		else if(isset($c[$champ]) && is_array(@unserialize($c[$champ])))
			$c[$champ] = $c[$champ];
		else
			$c[$champ] = '';
	}
	
	foreach(array(
		'titre','description'
	) as $texte){
		if(isset($c[$champ]))
			$c[$texte] = filtrer_entites($c[$texte]);
		else
			$c[$texte] = '';
	}

	$invalideur = "id='diogene/$id_diogene'";
	$indexation = true;
	
	if ($err = objet_modifier_champs('diogene', $id_diogene,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation
		),
		$c))
		return $err;	
		
	$rubriques = _request('id_rubrique',$set);
	sql_delete('spip_diogenes_liens','id_diogene='.intval($id_diogene).' AND objet="rubrique"');
	if(is_array($rubriques)){
		foreach($rubriques as $id_rubrique){
			sql_insertq('spip_diogenes_liens',array('id_diogene' => $id_diogene,'objet'=> $id_rubrique,'objet'=> 'rubrique'));
		}
	}

	$c = collecter_requests(array('id_secteur','objet','identifiant','type'),array(),$set);
	
	if(isset($c['identifiant'])){
		$c['type'] = $c['identifiant'];
		unset($c['identifiant']);
	}
	$err = diogene_instituer($id_diogene, $c);
	return $err;
}

function revision_diogene($id_diogene, $c=false) {
	return diogene_modifier($id_diogene,$c);
}
function diogene_set($id_diogene, $set=null) {
	return diogene_modifier($id_diogene,$set);
}
function insert_diogene($id_diogene, $c) {
	return diogene_inserer();
}
function instituer_diogene(){
	return diogene_instituer($id_diogene, $c);
}
?>