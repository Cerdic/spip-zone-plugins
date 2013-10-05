<?php
/**
 * Plugin Grappes
 * Licence GPL (c) Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

function action_editer_grappe_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (!$id_grappe = intval($arg))
		$id_grappe = grappe_inserer();

	if (!$id_grappe)
		return array(0,'');

	$err = grappe_modifier($id_grappe);

	return array($id_grappe,$err);
}


/**
 * Inserer une nouvelle grappe en base
 *
 * @return int $id_grappe
 * 	L'identifiant numérique de la nouvelle grappe
 */
function grappe_inserer() {

	$champs = array();
	$champs['date'] = date('Y-m-d H:i:s');
	$champs['id_admin'] = $GLOBALS['visiteur_session']['id_auteur'];
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_grappes',
			),
			'data' => $champs
		)
	);

	$id_grappe = sql_insertq("spip_grappes", $champs);
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_grappes',
				'id_objet' => $id_grappe
			),
			'data' => $champs
		)
	);

	return $id_grappe;
}

/**
 * Modifier une grappe
 *
 * $c est un contenu (par defaut on prend le contenu via _request())
 *
 * @param int $id_grappe
 * @param array|bool $set
 * @return string
 */
function grappe_modifier($id_grappe, $set=false) {
	
	include_spip('inc/modifier');
	
	$c = $opt = array();
	
	$c = collecter_requests(
		// white list
		objet_info('grappe','champs_editables'),
		// black list
		array('date'),
		// donnees eventuellement fournies
		$set
	);
	
	$opt['acces'] = $c['acces'];
	if(isset($c['acces']))
		unset($c['acces'],$set['acces']);
	$c['options'] = serialize($opt);
	
	if (is_array($c['liaisons']))
		$c['liaisons'] = implode(',',$c['liaisons']);
	
	$invalideur = "id='grappe/$id_grappe'";
	
	if ($err = objet_modifier_champs('grappe', $id_grappe,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
		),
		$c))
		return $err;

	// Modification de la date ?
	$c = collecter_requests(array('date'),array(''),$set);
	if(isset($c['liaisons']))
		unset($c['liaisons']);
	include_spip('action/editer_objet');
	$err = objet_instituer('grappe',$id_grappe, $c);

	return $err;
}

/**
 * Instituer une grappe
 *
 * @param int $id_grappe
 * @param array|bool $c
 * @return string
 */
function grappe_instituer($id_grappe, $c, $calcul_rub=true){
	// Envoyer aux plugins
	$c = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_grappes',
				'id_objet' => $id_grappe,
				'action'=>'instituer'
			),
			'data' => $c
		)
	);

	if (!count($c)) return;
	
	// Envoyer les modifs.
	sql_updateq('spip_grappes', $c, "id_grappe=$id_grappe");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_grappes',
				'id_objet' => $id_grappe,
				'action'=>'instituer'
			),
			'data' => $c
		)
	);

	return ''; // pas d'erreur
}

?>
