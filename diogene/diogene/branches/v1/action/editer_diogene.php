<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
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
		$id_diogene = insert_diogene();
	}

	// Enregistre l'envoi dans la BD
	if ($id_diogene > 0)
		$err = diogene_set($id_diogene);

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_diogene', $id_diogene, '&') . $err;

		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return array($id_diogene,$err);
}

/**
 * Fonction de mise à jour d'un template
 *
 * @param int $id_diogene
 * @param array $set un array des valeurs à mettre à jour
 */
function diogene_set($id_diogene, $set=null) {

	$c = array();
	foreach (array(
		'titre', 'description', 'champs_caches', 'champs_ajoutes','menu','statut_auteur','statut_auteur_publier','options_complements'
	) as $champ)
		$c[$champ] = _request($champ,$set);

	foreach(array(
		'champs_caches','champs_ajoutes','options_complements'
	) as $champ){
		if(is_array($c[$champ])){
			$c[$champ] = serialize($c[$champ]);
		}else if(is_array(@unserialize($c[$champ]))){
			$c[$champ] = $c[$champ];
		}
		else{
			$c[$champ] = '';
		}
	}
	
	foreach(array(
		'titre','description'
	) as $texte){
		$c[$texte] = filtrer_entites($c[$texte]);
	}

	include_spip('inc/modifier');
	revision_diogene($id_diogene, $c);

	$rubriques = _request('id_rubrique',$set);
	sql_delete('spip_diogenes_liens','id_diogene='.intval($id_diogene).' AND objet="rubrique"');
	if(is_array($rubriques)){
		foreach($rubriques as $id_rubrique){
			sql_insertq('spip_diogenes_liens',array('id_diogene' => $id_diogene,'objet'=> $id_rubrique,'objet'=> 'rubrique'));
		}
	}

	$c = array();
	foreach (array(
		'id_secteur','objet','type'
	) as $champ)
		$c[$champ] = _request($champ,$set);

	$err .= instituer_diogene($id_diogene, $c);

	return $err;
}

/**
 * Fonction de changement de statut d'un template
 *
 * @param unknown_type $id_diogene
 * @param unknown_type $c
 */
function instituer_diogene($id_diogene, $c) {

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
 */
function insert_diogene() {

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
 */
function diogene_supprimer($id_diogene){
	include_spip('inc/autoriser');
	$diogene = sql_fetsel('*','spip_diogenes','id_diogene='.intval($id_diogene));
	if($diogene && autoriser('modifier','diogene',$id_diogene)){
		sql_delete(
				'spip_diogenes',
				'id_diogene = '.intval($id_diogene)
			);
	}
}
?>