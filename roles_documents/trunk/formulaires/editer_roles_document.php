<?php

/**
 * Gestion du formulaire d'édition des rôles des liens d'un document
 *
 * `#FORMULAIRE_EDITER_ROLES_DOCUMENT{2,article,3}`
 * pour editer les roles du document 2 lié à l'article 3
 * 
 * @package SPIP\Formulaires
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire d'édition des rôles des liens d'un document
 *
 * @param int $id_document
 *     Identifiant du document
 * @param string $objet
 *     Type d'objet lié
 * @param int $id_objet
 *     Identifiant de l'objet lié
 * @param array $options
 *     Tableaux associatif d'options
 *     ajaxReload : sélecteur CSS d'un bloc à recharger
 * @return array
 */
function formulaires_editer_roles_document_charger_dist($id_document, $objet, $id_objet, $options = array()) {

	// charger les valeurs de editer_liens
	$editer_liens_charger = charger_fonction('charger', 'formulaires/editer_liens');
	$valeurs = $editer_liens_charger('spip_documents', $objet, $id_objet);

	// Renvoyer aussi id_document
	if (is_array($valeurs)) {
		$valeurs = array_merge($valeurs, array('id_document' => $id_document));
	}

	return $valeurs;
}

/**
 * Traiter le post des informations d'édition des rôles des liens d'un document
 *
 * On effectue les traitements par défaut de editer_liens, sauf qu'on s'assure de conserver l'unicité des rôles principaux (= logos)
 * 
 * @param int $id_document
 *     Identifiant du document
 * @param string $objet
 *     Type d'objet lié
 * @param int $id_objet
 *     Identifiant de l'objet lié
 * @param array $options
 *     Tableaux associatif d'options
 *     ajaxReload : sélecteur CSS d'un bloc à recharger
 * @return array
 */
function formulaires_editer_roles_document_traiter_dist($id_document, $objet, $id_objet, $options = array()) {

	include_spip('inc/editer_liens'); // inclus également les rôles
	$res = array();
	$done = false;

	// Récupérer les rôles principaux (=logos) de l'objet
	$roles = roles_documents_presents_sur_objet($objet, $id_objet, 0, true);

	// Action effectuée
	$ajouter = _request('ajouter_lien');
	$supprimer = _request('supprimer_lien');

	// Ajouter
	if ($ajouter) {
		$role = retrouver_action_role_document($ajouter);
		// Si c'est rôle principal (=logo) déjà attribué, on met à jour le lien existant
		if (in_array($role, $roles['attribues'])
			and $document_present = objet_trouver_liens(array('document' => '*'), array($objet => $id_objet), array('role=' . sql_quote($role)))
		) {
			$update = sql_updateq(
				'spip_documents_liens',
				array(
					'id_document' => $id_document,
				),
				array(
					'id_document='. intval($document_present[0]['id_document']),
					'objet=' . sql_quote($objet),
					'id_objet=' . intval($id_objet),
					'role=' . sql_quote($role),
				)
			);
			$done = true;
		}
	}

	// Si on a fait les traitements nous-même
	if ($done) {
		$res['editable'] = true;

	// Sinon traitements génériques de editer_liens
	} else {
		$editer_liens_traiter = charger_fonction('traiter', 'formulaires/editer_liens');
		$res = $editer_liens_traiter('spip_documents', $objet, $id_objet);
	}

	// recharger un ou plusieurs blocs après modification des roles
	// ajaxReload est un sélecteur css, tel que '#documents'
	if (!empty($options['ajaxReload'])) {
		$js = '<script type="text/javascript">';
		$js .= 'if (window.jQuery) jQuery("' . $options['ajaxReload'] . '").ajaxReload();';
		$js .= '</script>';
		if (isset($res['message_erreur'])) {
			$res['message_erreur'] .= $js;
		} elseif (isset($res['message_ok'])) {
			$res['message_ok'] .= $js;
		} else {
			$res['message_ok'] = $js;
		}
	}

	return $res;
}


/**
 * Fonction privée pour retrouver le rôle sélectionné dans une action.
 *
 * @param array $action
 *     Valeur du bouton = arguments séparés par des tirets
 *     document-id_document-objet-id_objet-role
 * @return string|false
 *     Le rôle de l'action, sinon false
 */
function retrouver_action_role_document($action) {

	$role = false;
	$action = array_shift(array_flip($action));
	if (is_array($arguments = explode('-', $action))
		and count($arguments) === 5
	) {
		$role = $arguments[4];
	}

	return $role;
}