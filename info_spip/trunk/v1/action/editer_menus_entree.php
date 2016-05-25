<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action de création / Modification d'une entrée de menu
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_editer_menus_entree_dist($arg=null) {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_menus_entree n'est pas un nombre, c'est une creation
	if (!$id_menus_entree = intval($arg)) {
		$id_menu = _request('id_menu_nouvelle_entree') ? _request('id_menu_nouvelle_entree') : _request('id_menu');
		if(intval($id_menu)){
			$id_menus_entree = insert_menus_entree($id_menu);
		}
	}

	// Enregistre l'envoi dans la BD
	if ($id_menus_entree > 0)
		$err = menus_entree_set($id_menus_entree);

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_menus_entree', $id_menus_entree, '&') . $err;

		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return array($id_menus_entree,$err);
}

/**
 * Crée une nouvelle entree de menu et retourne son ID
 *
 * @return int id_menus_entree
 */
function insert_menus_entree($id_menu) {
	// Envoyer aux plugins
	$champs = array();
	$champs['id_menu'] = $id_menu;
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_menus_entrees',
			),
			'data' => $champs
		)
	);

	$id_menus_entree = sql_insertq("spip_menus_entrees",array('id_menu'=>$id_menu));

	return $id_menus_entree;
}

/**
 * Appelle la fonction de modification d'une entrée de menu
 *
 * @param int $id_menu_entree
 * @param unknown_type $set
 * @return $err
 */
function menus_entree_set($id_menus_entree, $set=null) {
	$err = '';

	$c = array();
	$c['id_menu'] = _request('id_menu_nouvelle_entree');

	foreach (array(
		'rang',
		'type_entree',
		'parametres'
	) as $champ)
		$c[$champ] = _request($champ, $set);

	$c['parametres'] = is_array($c['parametres']) ? $c['parametres'] : array();
	$c['parametres'] = serialize($c['parametres']);

	include_spip('inc/menus');
	$entrees = menus_lister_disponibles();
	$infos = $entrees[$c['type_entree']];

	include_spip('inc/modifier');
	revision_menus_entree($id_menus_entree, $c);

	return $err;
}

/**
 * Enregistre une révision d'entree de menu
 *
 * @param int $id_menus_entree
 * @param array $c
 * @return
 */
function revision_menus_entree ($id_menus_entree, $c=false) {
	$invalideur = "id='id_menus_entree/$id_menus_entree'";

	modifier_contenu('menus_entree', $id_menus_entree,
		array(
			'invalideur' => $invalideur
		),
		$c);

	return ''; // pas d'erreur
}
?>