<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_tradlang_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_tradlang n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_tradlang = intval($arg)) {
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
		if (!($id_auteur)) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
	}

	// Enregistre l'envoi dans la BD
	if ($id_tradlang > 0) $err = tradlang_set($id_tradlang);

	if (_request('redirect')) {
		$redirect = parametre_url(urldecode(_request('redirect')),
			'id_tradlang', $id_tradlang, '&') . $err;
	
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
	else 
		return array($id_tradlang,$err);
}

function tradlang_set($id_tradlang,$c=''){
	$err = '';

	if(!is_array($c)){
		$c = array();
		foreach (array(
			'surtitre', 'titre', 'soustitre', 'descriptif',
			'nom_site', 'url_site', 'chapo', 'texte', 'ps'
		) as $champ)
			$c[$champ] = _request($champ);
	}

	include_spip('inc/modifier');
	revision_tradlang($id_tradlang, $c);

	return $err;
}

?>