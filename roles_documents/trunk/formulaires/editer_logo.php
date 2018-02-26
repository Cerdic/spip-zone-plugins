<?php


/**
 * Gestion du formulaire d'édition de logo
 *
 * Ce formulaire ajoute, modifie ou supprime des logos sur les objets de SPIP.
 *
 * - En dehors d'une boucle, ce formulaire modifie le logo du site.
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
$logo_libelles['site'] = _T('logo_site');
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
	if (!$objet) {
		$objet = 'site_spip';
	}
	$objet = objet_type($objet);

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

		$options['titre'] = $img . $libelle;
	}

	// Retrouver les rôles et les documents associés
	$roles = roles_presents('document', $objet); // Les rôles pour cet objet
	$roles_possibles = isset($roles['roles']['choix']) ? $roles['roles']['choix'] : array();
	$roles_possibles = filtrer_roles_logos($roles_possibles); // Les rôles de logos possibles pour cet objet
	$roles_attribues = roles_presents_sur_document($objet, $id_objet, true); // Les rôles de logos attribués pour cet objet
	$logos = array(); // Les documents logos utilisés avec leur rôle
	$config_logos = array(
		'logo'        => lire_config('activer_logos'),
		'logo_survol' => lire_config('activer_logos_survol'),
	);
	foreach ($roles_attribues as $role) {
		// Vérifier la config de certains rôles connus
		if (!in_array($role, array_keys($config_logos))
			or (
				in_array($role, array_keys($config_logos))
				and $config_logos[$role] == 'oui'
			)
		) {
			$logos[$role] = sql_getfetsel(
				'id_document',
				'spip_documents_liens',
				array(
					'objet='    . sql_quote($objet),
					'id_objet=' . intval($id_objet),
					'role='     . sql_quote($role),
				)
			);
		}
	}

	// Retrouver les vieux logos
	/*
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$vieux_logos = array();
	$_id_objet = id_table_objet($objet);
	foreach($config_logos as $role => $config) {
		$etat = ($role == 'logo' ? 'on' : 'off');
		if ($config == 'oui'
			and $logo = $chercher_logo($id_objet, $_id_objet, $etat)
		) {
			$vieux_logos[$etat] = $logo;
		}
	}*/

	// S'il y a moins de documents présents que de rôles possibles, on peut en ajouter
	$joindre_documents = count($roles_possibles) > count($logos);

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
		'editable'    => $editable,
		'logos'       => $logos,
		'objet'       => $objet,
		'id_objet'    => $id_objet,
		'role'        => '', // le rôle qui sera sélectionné
		'_options'    => $options,
		'editer_logo' => true, // Un flag pour identifier le contexte
		'vieux_logos' => $vieux_logos,
	);

	// Valeurs du formulaire d'ajout de document
	$charger_joindre_document = charger_fonction('charger', 'formulaires/joindre_document');
	$valeurs_joindre_document = $charger_joindre_document('new', $id_objet, $objet);

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
	$erreurs_joindre_document = $verifier_joindre_document('new', $id_objet, $objet);

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
	if (!$objet) {
		$objet = 'site_spip';
	}

	// Redirection
	if ($retour) {
		$res['redirect'] = $retour;
	}

	// Retours du formulaire d'ajout de document
	$traiter_joindre_document = charger_fonction('traiter', 'formulaires/joindre_document');
	$res_joindre_document = $traiter_joindre_document('new', $id_objet, $objet);

	// En cas de succès
	if (isset($res_joindre_document['message_ok'])) {

		// En présence d'un role sélectionne, on requalifie le lien créé
			if ($role = _request('role')
			and !empty($res_joindre_document['ids'])
		) {
			// On ne prend qu'un seul document
			if ($id_logo = intval(array_shift($res_joindre_document['ids']))) {
				$update = sql_updateq(
					'spip_documents_liens',
					array('role' => $role),
					array(
						'id_document=' . intval($id_logo),
						'objet='       . sql_quote($objet),
						'id_objet='    . intval($id_objet),
						'role='        . sql_quote('document'),
					)
				);
			}
		}

		// Invalider les caches de l'objet
		include_spip('inc/invalideur');
		suivre_invalideur("id='$objet/$id_objet'");

		// TODO : Modifier le javascript du message de retour

	}

	// On fusionne les retours
	$res = array_merge($res_joindre_document, $res);

	return $res;
}