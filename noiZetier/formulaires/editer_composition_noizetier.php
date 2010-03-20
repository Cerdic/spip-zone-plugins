<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_editer_composition_noizetier_charger($id, $nouveau){
	include_spip('inc/autoriser');
	$contexte = array();
	$contexte['editable'] = true;
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);

	// Seulement si on a le droit de configurer le noizetier
	if (autoriser('configurer', 'noizetier')){
		$nouveau = ($nouveau == 'oui') ? true : false;

		// Si on demande une composition
		if ($id!=''){
			// On désactive de toute façon le nouveau
			$nouveau = false;
			$type = noizetier_page_type($id);
			$compo = noizetier_page_composition($id);
			// On vérifie que cette composition existe
			// et si elle n'existe pas
			if (!is_array($noizetier_compositions[$type][$compo])){
				$contexte['editable'] = false;
				$contexte['message_erreur'] = _T('spip:erreur');
			}
			
		}
		elseif (!$nouveau){
			$contexte['editable'] = false;
			$contexte['message_erreur'] = _T('spip:erreur');
		}

		// Si on peut bien éditer la composition
		if ($contexte['editable']){
			// Les champs de la composition
			$contexte['type'] = '';
			$contexte['compo'] = '';
			$contexte['nom'] = '';
			$contexte['description'] = '';
			$contexte['icon'] = '';

			// Si la compo existe on prérempli
			if (isset($noizetier_compositions[$type][$compo])){
				$contexte['type'] = $type;
				$contexte['compo'] = $compo;
				$contexte['nom'] = $noizetier_compositions[$type][$compo]['nom'];
				$contexte['description'] = $noizetier_compositions[$type][$compo]['description'];
				$contexte['icon'] = $noizetier_compositions[$type][$compo]['icon'];
			}
			
			// Déclarer l'action pour SPIP 2.0
			$contexte['_action'] = array('editer_composition_noizetier', $id);
			
			if ($nouveau) {
				$contexte['_hidden'] .= '<input type="hidden" name="nouveau" value="oui" />';
				$contexte['nouveau'] = oui;
			}
		}
	}
	else{
		$contexte['editable'] = false;
	}

	return $contexte;
}

function formulaires_editer_composition_noizetier_verifier($id, $nouveau){
	$erreurs = array();
	foreach(array('type','compo','nom') as $champ) {
		if (!_request($champ)) {
			$erreurs[$champ] = _T('noizetier:formulaire_obligatoire');
		}
	}
	// On vérifie, dans le cas d'une nouvelle composition que $compo n'est pas déjà pris (compo du noizetier ou compo existante).
	// On vérifie aussi que $compo ne contient ni espace, ni tiret, ni 
	if (_request('nouveau')=='oui' AND _request('compo')) {
		include_spip('inc/noizetier');
		$type = _request('type');
		$compo = _request('compo');
		$liste_pages = noizetier_lister_pages();
		if (is_array($liste_pages[$type.'-'.$compo]))
			$erreurs['compo'] = _T('noizetier:formulaire_identifiant_deja_pris');
		if (preg_match('#^[a-z0-9_]+$#',$compo)==0)
			$erreurs['compo'] = _T('noizetier:formulaire_erreur_format_identifiant');
	}

	return $erreurs;
}

function formulaires_editer_composition_noizetier_traiter($id, $nouveau){
	include_spip('inc/autoriser');
	$retours = array();
	if (autoriser('configurer', 'noizetier')){
		$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
		$type = _request('type');
		$compo = _request('compo');
		
		// Au cas où on n'a pas encore configuré de compositions
		if (!is_array($noizetier_compositions))
			$noizetier_compositions = array();
		
		$noizetier_compositions[$type][$compo] = array(
			'nom' => _request('nom'),
			'description' => _request('description'),
			'icon' => _request('icon')
		);
		ecrire_meta('noizetier_compositions',serialize($noizetier_compositions));
		$retours['message_ok'] = _T('noizetier:formulaire_composition_mise_a_jour');
		
		// Si on est dans l'espace privé, on redirige vers la liste des compos
		if (_request('exec') == 'noizetier_composition_editer')
			$retours['redirect'] = generer_url_ecrire('noizetier_compositions');
	}
	$retours['editable'] = true;
	return $retours;
}

?>
