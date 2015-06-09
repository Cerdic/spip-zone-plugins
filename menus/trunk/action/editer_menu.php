<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action de création / Modification d'un menu
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_editer_menu_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// si id_menu n'est pas un nombre, c'est une creation
	if (!$id_menu = intval($arg)) {
		$id_menu = insert_menu();
	}

	// Enregistre l'envoi dans la BD
	if ($id_menu > 0) $err = menu_set($id_menu);

	// S'il y a un fichier on tente d'importer son contenu
	if (isset($_FILES['import']) AND $_FILES['import']) {
		$fichier = $_FILES['import']['tmp_name'];
		$yaml = '';
		lire_fichier($fichier, $yaml);
		// Si on a bien recupere une chaine on tente de la decoder
		if ($yaml){
			include_spip('inc/yaml');
			$entrees = yaml_decode($yaml);
			// Si le decodage marche on importe alors le contenu
			if (is_array($entrees)){
				menus_importer($entrees, $id_menu);
			}
		}
	}

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_menu', $id_menu, '&') . $err;

		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else
		return array($id_menu,$err);
}

/**
 * Appelle la fonction de modification d'un menu
 *
 * @param int $id_menu
 * @param unknown_type $set
 * @return $err
 */
function menu_set($id_menu, $set=null) {
	$err = '';

	$c = array();
	foreach (array(
		'titre',
		'identifiant',
		'css'
	) as $champ)
		$c[$champ] = _request($champ,$set);

	include_spip('inc/modifier');
	revision_menu($id_menu, $c);

	return $err;
}

/**
 * Crée un nouveau menu et retourne son ID
 *
 * @return int id_menu
 */
function insert_menu() {
	$champs = array('titre'=>''); // eviter le bug de req/sqlite < 2.1.3
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_menus',
			),
			'data' => $champs
		)
	);
	$id_menu = sql_insertq("spip_menus");

	return $id_menu;
}

/**
 * Enregistre une révision de menu
 *
 * @param int $id_menu
 * @param array $c
 * @return
 */
function revision_menu ($id_menu, $c=false) {
	$invalideur = "id='id_menu/$id_menu'";

	modifier_contenu('menu', $id_menu,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur
		),
		$c);

	return ''; // pas d'erreur
}

function menus_importer($entrees, $id_menu){
	// On lit chaque entree de premier niveau
	foreach ($entrees as $cle => $entree){
		// On ajoute cette entree
		$id_menus_entree = sql_insertq(
			'spip_menus_entrees',
			array(
				'id_menu' => $id_menu,
				'rang' => ($cle+1),	// les entrees sont dans l'ordre des rangs
				'type_entree' => $entree['type_entree'],
				'parametres' => serialize($entree['parametres'])
			)
		);

		// S'il existe un sous-menu pour cette entree on le cree
		if (is_array($entree['sous_menu'])){
			$id_sous_menu = sql_insertq(
				'spip_menus',
				array(
					'id_menus_entree' => $id_menus_entree
				)
			);
			// Puis dedans on importe les entrees correspondantes
			menus_importer($entree['sous_menu'], $id_sous_menu);
		}
	}
}
?>