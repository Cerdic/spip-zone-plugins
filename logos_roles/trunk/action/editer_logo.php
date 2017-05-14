<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2016                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion de l'API de modification/suppression des logos
 *
 * @package SPIP\Core\Logo\Edition
 */

/**
 * Supprimer le logo d'un objet
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $role
 *     Un rôle de logo. `on` ou `off` sont aussi admis pour rétro-compatibilité
 *
 * @return string|null
 *     Erreur, sinon rien
 */
function logo_supprimer($objet, $id_objet, $role) {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$objet = objet_type($objet);
	$primary = id_table_objet($objet);

	if ($role === 'on') {
		$role = 'logo';
	} elseif ($role === 'off') {
		$role = 'logo_survol';
	}

	$logo = $chercher_logo($id_objet, $primary, $role);
	// si pas de logo ou qu'on est dans le cas d'un logo attribué par défaut, on
	// ne fait rien
	if ((! $logo) or
			(($role != 'logo') and est_logo_par_defaut($logo[0], $id_objet, $objet, $role))) {
		return;
	}

	include_spip('base/abstract_sql');
	include_spip('action/editer_liens');
	// S'il n'y pas de document qui gère le logo, on le supprime avec la méthode
	// historique
	if (! objet_trouver_liens(array('document' => '*'), array($objet => $id_objet), array('role' => $role))) {
		spip_unlink($logo[0]);
	} else {
		// Si le logo est géré par un document on ne supprime que le lien
		if ($objet === 'site') {
			sql_delete(
				'spip_documents_liens',
				array('objet="site"', 'id_objet=0', 'role='.sql_quote($role))
			);

			include_spip('inc/config');
			$logos_site = lire_config('logos_site');
			unset($logos_site[$role]);
			ecrire_config('logos_site', $logos_site);
		} else {
			objet_dissocier(
				array('document' => '*'),
				array($objet => $id_objet),
				array('role' => $role)
			);
		}

		// Si le plugin massicot est installé, on doit aussi supprimer le
		// massicotage correspondant.
		if (test_plugin_actif('massicot')) {
			if ($err = massicot_supprimer($objet, $id_objet, $role)) {
				return $err;
			}
		}
	}
}

/**
 * Modifier le logo d'un objet à partir d'un fichier uploadé
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $role
 *     le role, ou `on` ou `off` pour la rétro-compatibilité
 * @param string|array $source
 *     - array : sous tableau de `$_FILE` issu de l'upload
 *     - string : fichier source (chemin complet ou chemin relatif a `tmp/upload`)
 * @return string
 *     Erreur, sinon ''
 */
function logo_modifier($objet, $id_objet, $role, $source) {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$objet = objet_type($objet);
	$primary = id_table_objet($objet);
	include_spip('inc/chercher_logo');
	$type = type_du_logo($primary);

	// supprimer le logo éventuel existant
	logo_supprimer($objet, $id_objet, $role);

	if (!$source) {
		spip_log('spip_image_ajouter : source inconnue');
		return $erreur = 'source inconnue';
	}

	include_spip('action/ajouter_documents');
	$ajouter_un_document = charger_fonction('ajouter_un_document', 'action');
	// On ne passe pas l'objet, pour éviter de créer un lien avec le rôle par
	// défaut. On fait le lien à la main un peu plus bas.
	$id_document = $ajouter_un_document('new', $source, null, null, 'image');
	if (is_string($id_document)) {
		return $erreur = $id_document;
	}

	return logo_modifier_document($objet, $id_objet, $role, $id_document);
}

/**
 * Modifier le logo d'un objet à partir d'un document
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $role
 *     le role, ou `on` ou `off` pour la rétro-compatibilité
 * @param integer $id_document : l'identifiant du document
 * @return string
 *     Erreur, sinon ''
 */
function logo_modifier_document($objet, $id_objet, $role, $id_document) {

	// Cas du LOGO_SITE_SPIP..
	if (($objet === 'site') and ($id_objet == 0)) {
		include_spip('base/abstract_sql');

		// On supprime d'éventuels liens existants
		sql_delete(
			'spip_documents_liens',
			array('objet="site"', 'id_objet=0', 'role='.sql_quote($role))
		);

		// Puis on insère le nouveau
		sql_insertq(
			'spip_documents_liens',
			array(
				'id_document' => intval($id_document),
				'objet' => 'site',
				'id_objet' => 0,
				'role' => $role,
			)
		);

		// On enregistre les logos du site dans une meta, pour pouvoir les rétablir
		// automatiquement après le passage du CRON d'optimisation, qui efface les
		// liens vers des id_objet qui valent 0.
		include_spip('inc/config');

		$logos_site = lire_config('logos_site') ?: array();
		$logos_site[$role] = intval($id_document);
		ecrire_config('logos_site', $logos_site);

	// Cas des autres logos
	} else {
		include_spip('action/editer_liens');
		objet_associer(
			array('document' => $id_document),
			array($objet => $id_objet),
			array('role' => $role)
		);
	}

	return $erreur = '';
}
