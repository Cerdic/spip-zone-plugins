<?php

/**
 * Gestion du formulaire d'édition des rôles des liens d'un document
 *
 * @package SPIP\Formulaires
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire d'édition des rôles des liens d'un document
 *
 * `#FORMULAIRE_EDITER_ROLES_DOCUMENT{2,article,3}`
 * pour editer les roles du document 2 lié à l'article 3
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

	// retourner les traitements de editer_liens
	$editer_liens_traiter = charger_fonction('traiter', 'formulaires/editer_liens');
	$res = $editer_liens_traiter('spip_documents', $objet, $id_objet);

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
