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

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Supprimer le logo d'un objet
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $role
 *     `on` ou `off` pour rétro-compatibilité, sinon un role de logo
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
	// si pas de logo ou qu'on est dans le cas d'un logo par défaut, on ne fait rien
	$logo_defaut = $chercher_logo($id_objet, $primary, 'on');
	if ((! $logo) or (($role !== 'logo') and ($logo[0] === $logo_defaut[0]))) {
		return;
	}

	include_spip('action/editer_liens');
	// S'il n'y pas de document qui gère le logo, on le supprime avec la méthode
	// historique
	if (! objet_trouver_liens(array('document' => '*'), array($objet => $id_objet), array('role' => $role))) {
		spip_unlink($logo[0]);
	} else {
		// Si le logo est géré par un document on ne supprime que le lien
		objet_dissocier(
			array('document' => '*'),
			array($objet => $id_objet),
			array('role' => $role)
		);
	}
}

/**
 * Modifier le logo d'un objet
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $etat
 *     `on` ou `off`
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

	include_spip('action/editer_liens');
	objet_associer(
		array('document' => $id_document),
		array($objet => $id_objet),
		array('role' => $role)
	);

	return $erreur = '';
}
