<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_menu
 *     Identifiant du menu. 'new' pour un nouveau menu.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le menu créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un menu source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du menu, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_menu_identifier_dist($id_menu = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_menu), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de menu
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_menu
 *     Identifiant du menu. 'new' pour un nouveau menu.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le menu créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un menu source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du menu, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_menu_charger($id_menu = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	include_spip('base/abstract_sql');
	include_spip('inc/autoriser');
	$contexte = array();
	$contexte['editable'] = true;

	// Seulement si on a le droit de modifier les menus
	if (autoriser('modifier', 'menu')) {
		$nouveau = intval($id_menu) ? false : true;
		$id_menu = intval($id_menu) ? intval($id_menu) : false;

		// Si on demande un id_menu
		if ($id_menu and !$nouveau) {
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
			if (!$id_menu_ok) {
				$contexte['editable'] = false;
				$contexte['message_erreur'] = _T('menus:erreur_menu_inexistant', array('id'=>$id_menu));
			}
		} elseif (!$nouveau) {
			$contexte['editable'] = false;
			$contexte['message_erreur'] = _T('menus:erreur_parametres');
		}

		// Si on peut bien éditer le menu, on déclare ce qu'il faut
		if ($contexte['editable']) {
			$contexte['id_menu'] = $id_menu;
			$contexte['nouveau'] = $nouveau;

			// Les champs du menu principal
			$contexte['titre'] = '';
			$contexte['identifiant'] = '';
			$contexte['css'] = '';
			$contexte['import'] = '';

			$valeurs = formulaires_editer_objet_charger('menu', $id_menu, 0, 0, '', '', array(), '');

			$contexte = array_merge($contexte, $valeurs);

			// Déclarer l'action pour SPIP 2.0
			$contexte['_action'] = array('editer_menu', $id_menu);
			// On sait toujours si on est sur un menu déjà créé ou pas
			$contexte['_hidden'] .= '<input type="hidden" name="id_menu" value="'.$id_menu.'" />';
			// reinjecter nouveau si besoin, sinon la page de l'espace prive ne reaffiche pas le form
			$contexte['_hidden'] .= "<input type='hidden' name='nouveau' value='".($nouveau?'oui':'')."' />";
		}
	} else {
		$contexte['editable'] = false;
		$contexte['message_erreur'] = _T('menus:erreur_autorisation');
	}

	return $contexte;
}

/**
 * Vérifications du formulaire d'édition de menu
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_menu
 *     Identifiant du menu. 'new' pour un nouveau menu.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le menu créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un menu source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du menu, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_menu_verifier($id_menu = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {

	// On vérifie que les champs obligatoires sont bien saisis.
	$oblis = array('titre','identifiant');
	$erreurs = formulaires_editer_objet_verifier('menu', $id_menu, $oblis);

	// On vérifie que l'identifiant est bon.
	$identifiant = _request('identifiant');
	if (empty($erreurs['identifiant']) and !preg_match('/^[\w-]+$/', $identifiant)) {
		$erreurs['identifiant'] = _T('menus:erreur_identifiant_forme');
	}
	// On vérifie que l'identifiant n'est pas déjà utilisé
	include_spip('base/abstract_sql');
	if (empty($erreurs['identifiant'])) {
		$deja = sql_getfetsel(
			'id_menu',
			'spip_menus',
			array(
				'identifiant = '.sql_quote($identifiant),
				'id_menu > 0',
				'id_menu !='.intval(_request('id_menu'))
			)
		);
		if ($deja) {
			$erreurs['identifiant'] = _T('menus:erreur_identifiant_deja');
		}
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de menu
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_menu
 *     Identifiant du menu. 'new' pour un nouveau menu.
 * @param string $redirect
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le menu créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un menu source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du menu, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_menu_traiter($id_menu = 'new', $redirect = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$retour = formulaires_editer_objet_traiter('menu', $id_menu, '', $lier_trad, $redirect, $config_fonc, $row, $hidden);

	// Si ça va pas on errorise
	if (!$retour['id_menu']) {
		$retour['message_erreur'] = _T('menus:erreur_mise_a_jour');
	} else {
		// Si on est dans l'espace privé on force la redirection
		if (_request('exec') == 'menus_editer') {
			$retour['redirect'] = generer_url_ecrire('menus_editer', 'id_menu='.$retour['id_menu']);
		}
	}

	// Un lien a prendre en compte ?
	if ($associer_objet and $id_menu = $retour['id_menu']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			
			objet_associer(array('menu' => $id_menu), array($objet => $id_objet));
			
			if (isset($retour['redirect'])) {
				$retour['redirect'] = parametre_url($retour['redirect'], 'id_lien_ajoute', $id_menu, '&');
			}
		}
	}

	// Si c'est une création et qu'il n'y a pas déjà de redirection,
	// on renvoie sur la page d'édition du menu pour ajouter les entrées
	if (
		!intval($id_menu)
		and $retour['id_menu']
		and !$redirect
	) {
		$retour['redirect'] = generer_url_ecrire_entite_edit($retour['id_menu'], 'menu');
	}

	// Dans tous les cas le formulaire est toujours éditable
	$retour['editable'] = true;

	return $retour;
}
