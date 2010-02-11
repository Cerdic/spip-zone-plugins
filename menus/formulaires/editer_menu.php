<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_editer_menu_charger($id_menu, $nouveau){
	include_spip('base/abstract_sql');
	include_spip('inc/autoriser');
	$contexte = array();
	$contexte['editable'] = true;

	// Seulement si on a le droit de modifier les menus
	if (autoriser('modifier', 'menu')){
		$nouveau = ($nouveau == 'oui') ? true : false;
		$id_menu = intval($id_menu) ? intval($id_menu) : false;

		// Si on demande un id_menu
		if ($id_menu){
			// On désactive de toute façon le nouveau
			$nouveau = false;

			// On teste si le menu existe bien dans les menus principaux
			$id_menu_ok = intval(sql_getfetsel(
				'id_menu',
				'spip_menus',
				array(
					array('=', 'id_menu', $id_menu),
					array('=', 'id_menus_entree', 0)
				)
			));

			// S'il n'existe pas
			if (!$id_menu_ok){
				$contexte['editable'] = false;
				$contexte['message_erreur'] = _T('menus:erreur_menu_inexistant', array('id'=>$id_menu));
			}
		}
		elseif (!$nouveau){
			$contexte['editable'] = false;
			$contexte['message_erreur'] = 'erreur parametres';
		}

		// Si on peut bien éditer le menu, on déclare ce qu'il faut
		if ($contexte['editable']){
			$contexte['id_menu'] = $id_menu;

			// Les champs du menu principal
			$contexte['titre'] = '';
			$contexte['identifiant'] = '';
			$contexte['css'] = '';
			$contexte['import'] = '';

			// Si le menu existe on prérempli
			if ($id_menu){
				$menu = sql_fetsel(
					'titre, identifiant, css',
					'spip_menus',
					'id_menu='.$id_menu
				);
				$contexte = array_merge($contexte, $menu);
			}

			// On sait toujours si on est sur un menu déjà créé ou pas
			$contexte['_hidden'] .= '<input type="hidden" name="id_menu" value="'.$id_menu.'" />';
			$contexte['_hidden'] .= '<input type="hidden" name="nouveau" value="'.$nouveau.'" />';
		}
	}
	else{
		$contexte['editable'] = false;
		$contexte['message_erreur'] = _T('menus:erreur_autorisation');
	}

	return $contexte;
}

function formulaires_editer_menu_verifier($id_menu, $nouveau){
	include_spip('base/abstract_sql');
	$erreurs = array();

	// On vérifie que tout est rempli
	if (!_request('titre'))
		$erreurs['titre'] = _T('info_obligatoire');
	if (!$identifiant = _request('identifiant'))
		$erreurs['identifiant'] = _T('info_obligatoire');

	// On vérifie que l'identifiant est bon
	if (!$erreurs['identifiant'] and !preg_match('/^[\w]+$/', $identifiant))
		$erreurs['identifiant'] = _T('menus:erreur_identifiant_forme');
	// On vérifie que l'identifiant n'est pas déjà utilisé
	if (!$erreurs['identifiant']){
		$deja = sql_getfetsel(
			'id_menu',
			'spip_menus',
			array(
				'identifiant = '.sql_quote($identifiant),
				'id_menu > 0',
				'id_menu !='.intval(_request('id_menu'))
			)
		);
		if ($deja)
			$erreurs['identifiant'] = _T('menus:erreur_identifiant_deja');
	}

	return $erreurs;
}

function formulaires_editer_menu_traiter($id_menu, $nouveau){
	$res = formulaires_editer_objet_traiter('menu', $id_menu, 0, 0, '', '', '', '');

	// Si ça va pas on errorise
	if (!$res['id_menu']){
		$res['message_erreur'] = _T('menus:erreur_mise_a_jour');
	}
	else{
		// Si on est dans l'espace privé on force la redirection
		if (_request('exec') == 'menus_editer')
			$res['redirect'] = generer_url_ecrire('menus_editer', "id_menu=".$res['id_menu']);
	}
	// Dans tous les cas le formulaire est toujours éditable
	$res['editable'] = true;

	return $res;
}

?>
