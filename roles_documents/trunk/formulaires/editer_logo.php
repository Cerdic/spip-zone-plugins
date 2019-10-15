<?php


/**
 * Gestion du formulaire d'édition de logo
 *
 * Ce formulaire ajoute, modifie ou supprime des logos sur les objets de SPIP.
 *
 * - En dehors d'une boucle, ce formulaire modifie le logo du site (lié à un pseudo-objet 'site_spip').
 * - Dans une boucle, il modifie le logo de la table selectionnée.
 *
 * - il est possible de lui passer les paramètres objet et id : `#FORMULAIRE_EDITER_LOGO{article,1}`
 * - il est possible de spécifier une URL de redirection apres traitement :
 *   `#FORMULAIRE_EDITER_LOGO{article,1,#URL_ARTICLE}`
 *
 * @package SPIP\Core\Formulaires
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

global $logo_libelles;
// utilise pour le logo du site, donc doit rester ici
$logo_libelles['site_spip'] = _T('logo_site');
$logo_libelles['racine'] = _T('logo_standard_rubrique');


/**
 * Chargement du formulaire d'édition de logo
 *
 * @param string $objet Objet SPIP auquel sera lie le document (ex. article)
 * @param int $id_objet Identifiant de l'objet
 * @param string $retour Url de redirection apres traitement
 * @param array $options Tableau d'option (exemple : image_reduire => 50)
 * @return array               Variables d'environnement pour le fond
 */
function formulaires_editer_logo_charger_dist($objet, $id_objet, $retour = '', $options = array()) {

	include_spip('inc/config');
	include_spip('inc/roles');

	// Pas dans une boucle ? formulaire pour le logo du site
	if (!$objet
		or (
			$objet == 'site'
			and intval($id_objet) <= 0
		)
	) {
		$objet = 'site_spip';
		$id_objet = -1;
	}
	$objet = objet_type($objet);
	$id_table_objet = id_table_objet($objet);

	// Options
	if (!is_array($options)) {
		$options = unserialize($options);
	}

	// Retrouver le titre
	if (!isset($options['titre'])) {
		$balise_img = chercher_filtre('balise_img');
		$libelles = pipeline('libeller_logo', $GLOBALS['logo_libelles']);
		$libelle = (($id_objet or $objet != 'rubrique') ? $objet : 'racine');
		if (isset($libelles[$libelle])) {
			$libelle = $libelles[$libelle];
		} elseif ($libelle = objet_info($objet, 'texte_logo_objet')) {
			$libelle = _T($libelle);
		} else {
			$libelle = _L('Logo');
		}
		switch ($objet) {
			case 'article':
				$libelle .= ' ' . aider('logoart');
				break;
			case 'breve':
				$libelle .= ' ' . aider('breveslogo');
				break;
			case 'rubrique':
				$libelle .= ' ' . aider('rublogo');
				break;
			default:
				break;
		}

		$options['titre'] = $libelle;
	}

	// Chercher les logos
	$logos = array();
	$config = array(
		lire_config('activer_logos'),
		lire_config('activer_logos_survol'),
	);

	// =====================================
	// 1) Cherchons en pririté les documents
	// =====================================
	// Rôles principaux de l'objet (= rôles de logos)
	$infos_roles = roles_presents('document', $objet);
	$roles_principaux = isset($infos_roles['roles']['principaux']) ? $infos_roles['roles']['principaux'] : array('logo', 'logo_survol');
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	// Rôles principaux attribués...
	$roles_logos = roles_documents_presents_sur_objet($objet, $id_objet, 0, true);
	// Cherchons le document pour chaque rôle attribué
	foreach ($roles_logos['attribues'] as $role) {
		// Vérifier la config : si le rôle est dans la liste des rôles principaux, on regarde à quelle position il se trouve et on prend la config correspondante, sinon on ignore.
		$position = array_search($role, $roles_principaux);
		$config_actif = isset($config[$position]) ? $config[$position] == 'oui' : true;
		if ($config_actif
			and $logo = $chercher_logo($id_objet, $id_table_objet, $role)
		) {
			$logos[] = $logo;
		}
	}

	// ====================================
	// 2) Cherchons ensuite les vieux logos
	// ====================================
	$etats = array('on', 'off');
	foreach($etats as $k => $etat) {
		if ($config[$k] == 'oui'
			and $logo = $chercher_logo($id_objet, $id_table_objet, $etat, true)
		) {
			$logos[] = $logo;
			// On ajuste les rôles attribués (en faisant correspondre avec les rôles principaux)
			if (isset($roles_principaux[$k])) {
				array_push($roles_logos['attribues'], $roles_principaux[$k]);
				unset($roles_principaux[$k]);
				$roles_logos['attribuables'] = [];
			}
		}
	}
	$roles_logos['attribues'] = array_unique($roles_logos['attribues']);

	// S'il y a moins de rôles attribués que de rôles possibles, on peut en ajouter
	$joindre_documents = count($roles_logos['possibles']) > count($roles_logos['attribues']);

	// Autorisation
	if (!isset($options['editable'])) {
		include_spip('inc/autoriser');
		$options['editable'] = autoriser('iconifier', $objet, $id_objet);
	}
	$editable = (
		(lire_config('activer_logos') == 'oui' ? true : false)
		and (!isset($options['editable']) or $options['editable'])
		and $joindre_documents
	);

	// Valeurs initiales
	$valeurs = array(
		'editable'           => $editable,
		'logos'              => $logos,
		'objet'              => $objet,
		'id_objet'           => $id_objet,
		'roles'              => '', // le rôle qui sera sélectionné
		'roles_attribuables' => $roles_logos['attribuables'], // rôles attribuables
		'_options'           => $options,
		'editer_logo'        => true, // Un flag pour identifier le contexte
	);

	// Valeurs du formulaire d'ajout de document
	$charger_joindre_document = charger_fonction('charger', 'formulaires/joindre_document');
	$valeurs_joindre_document = $charger_joindre_document('new', $id_objet, $objet, 'choix');

	// On fusionne les valeurs
	$valeurs = array_merge($valeurs_joindre_document, $valeurs);

	// Si le logo n'est pas editable et qu'il n'y en a pas,
	// on n'affiche pas du tout le formulaire
	if ((!$valeurs['editable']
			and empty($logos)
		)
		or (lire_config('activer_logos') != 'oui')
	) {
		return false;
	}

	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 *
 * @param string $objet Objet SPIP auquel sera lie le document (ex. article)
 * @param int $id_objet Identifiant de l'objet
 * @param string $retour Url de redirection apres traitement
 * @param array $options Tableau d'option (exemple : image_reduire => 50)
 * @return string              Hash du formulaire
 */
function formulaires_editer_logo_identifier_dist($objet, $id_objet, $retour = '', $options = array()) {
	return serialize(array($objet, $id_objet));
}

/**
 * Verification avant traitement du formulaire d'édition de logo
 *
 * On verifie que l'upload s'est bien passe et
 * que le document recu est une image (d'apres son extension)
 *
 * @param string $objet Objet SPIP auquel sera lie le document (ex. article)
 * @param int $id_objet Identifiant de l'objet
 * @param string $retour Url de redirection apres traitement
 * @param array $options Tableau d'option (exemple : image_reduire => 50)
 * @return array               Erreurs du formulaire
 */
function formulaires_editer_logo_verifier_dist($objet, $id_objet, $retour = '', $options = array()) {

	// Vérifications propres aux logos
	$erreurs = array();

	// Vérifications du formulaire d'ajout de document
	$verifier_joindre_document = charger_fonction('verifier', 'formulaires/joindre_document');
	$erreurs_joindre_document = $verifier_joindre_document('new', $id_objet, $objet, 'choix');

	// On fusionnes les erreurs
	$erreurs = array_merge($erreurs_joindre_document, $erreurs);

	return $erreurs;
}

/**
 * Traitement de l'upload d'un logo
 *
 * Il est affecte au site si la balise n'est pas dans une boucle,
 * sinon a l'objet concerne par la boucle ou indiquee par les parametres d'appel
 *
 * @param string $objet Objet SPIP auquel sera lie le document (ex. article)
 * @param int $id_objet Identifiant de l'objet
 * @param string $retour Url de redirection apres traitement
 * @param array $options Tableau d'option (exemple : image_reduire => 50)
 * @return array               Retour des traitements
 */
function formulaires_editer_logo_traiter_dist($objet, $id_objet, $retour = '', $options = array()) {

	// Retours
	$res = array('editable' => true);

	// Pas dans une boucle ? formulaire pour le logo du site
	if (!$objet
		or (
			$objet == 'site'
			and intval($id_objet) <= 0
		)
	) {
		$objet = 'site_spip';
		$id_objet = -1;
	}

	// Redirection
	if ($retour) {
		$res['redirect'] = $retour;
	}

	// refdoc_joindre peut être un identifiant saisi à la main, ou une référence
	// retournée par la modale de sélection de la médiathèque, sous la forme docXX.
	preg_match('/^(doc)?([0-9]+)$/i', _request('refdoc_joindre'), $m);
	$refdoc_joindre = isset($m[2]) ? $m[2] : 0;

	// Si c'est un doc de la médiathèque, notons s'il est déjà lié
	$doc_deja_present = (
		_request('joindre_mediatheque')
		and sql_countsel(
			'spip_documents_liens',
			array(
				'objet = ' . sql_quote($objet),
				'id_objet = ' . intval($id_objet),
				'id_document = ' . intval($refdoc_joindre),
				'id_document > 0'
			)
		)
	);

	// Traitements génériques du formulaire d'ajout de documents
	// (ajout du doc dans la table, liaison avec l'objet, etc.)
	$traiter_joindre_document = charger_fonction('traiter', 'formulaires/joindre_document');
	$res_joindre_document = $traiter_joindre_document('new', $id_objet, $objet, 'choix');

	// En cas de succès, on ajoute le rôle sélectionné
	if ($roles = _request('roles')
		and isset($res_joindre_document['message_ok'])
		and !empty($res_joindre_document['ids'])
	) {

		// Un seul rôle peut être sélectionné, mais on ne sait jamais
		if (is_array($roles)) {
			$roles = array_shift($roles);
		}

		// On ne prend qu'un seul document
		$id_document = intval(array_shift($res_joindre_document['ids']));

		// Cas 1 : le document n'était pas déjà lié, on requalifie le lien créé
		if (!$doc_deja_present) {
			$update = sql_updateq(
				'spip_documents_liens',
				array('role' => $roles),
				array(
					'id_document=' . intval($id_document),
					'objet='       . sql_quote($objet),
					'id_objet='    . intval($id_objet),
					'role='        . sql_quote('document'),
				)
			);

		// Cas 2 : le document était déjà lié, on crée un nouveau lien qualifié
		} else {
			$insert = sql_insertq(
				'spip_documents_liens',
				array(
					'id_document' => intval($id_document),
					'objet'       => $objet,
					'id_objet'    => intval($id_objet),
					'role'        => $roles,
				)
			);
		}

		// Invalider les caches de l'objet
		include_spip('inc/invalideur');
		suivre_invalideur("id='$objet/$id_objet'");

	}

	// On fusionne les retours
	$res = array_merge($res_joindre_document, $res);

	return $res;
}