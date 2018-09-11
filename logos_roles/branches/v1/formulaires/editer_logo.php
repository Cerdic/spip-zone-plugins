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
 * Gestion du formulaire d'édition de logo
 *
 * Ce formulaire ajoute, modifie ou supprime des logos sur les objets de SPIP.
 *
 * - En dehors d'une boucle, ce formulaire modifie le logo du site.
 * - Dans une boucle, il modifie le logo de la table selectionnée.
 *
 * Pensez juste que l'appel de `#LOGO_{TYPE}` s'appuie sur le nom de la clé primaire et non sur le
 * nom de l'objet réel. Par exemple on ecrira `#LOGO_GROUPE` (et non `#LOGO_GROUPEMOTS`) pour afficher
 * un logo issu du formulaire mis dans une boucle `GROUPES_MOTS`
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
	// pas dans une boucle ? formulaire pour le logo du site
	// dans ce cas, il faut chercher un 'siteon0.ext'
	if (!$objet) {
		$objet = 'site';
	}

	$objet = objet_type($objet);
	$_id_objet = id_table_objet($objet);

	if (!is_array($options)) {
		$options = unserialize($options);
	}

	if (!isset($options['titre'])) {
		$balise_img = chercher_filtre('balise_img');
		$img = $balise_img(chemin_image('image-24.png'), '', 'cadre-icone');
		$libelles = pipeline('libeller_logo', $GLOBALS['logo_libelles']);
		$libelle = (($id_objet or $objet != 'rubrique') ? $objet : 'racine');
		if (isset($libelles[$libelle])) {
			$libelle = $libelles[$libelle];
		} elseif ($libelle = objet_info($objet, 'texte_logo_objet')) {
			$libelle = _T($libelle);
		} else {
			$libelle = _L('Logo');
		}

		// Depuis SPIP 3.2, 'aider' est directement une fonction. Avant il fallait
		// utiliser 'charger_fonction'.
		if (function_exists('aider')) {
			$aider = 'aider';
		} else {
			$aider = charger_fonction('aider', 'inc', true);
		}

		switch ($objet) {
			case 'article':
				$libelle .= ' ' . $aider('logoart');
				break;
			case 'breve':
				$libelle .= ' ' . $aider('breveslogo');
				break;
			case 'rubrique':
				$libelle .= ' ' . $aider('rublogo');
				break;
			default:
				break;
		}

		$options['titre'] = $img . $libelle;
	}
	if (!isset($options['editable'])) {
		include_spip('inc/autoriser');
		$options['editable'] = autoriser('iconifier', $objet, $id_objet);
	}

	$res = array(
		'editable' => (count(lister_roles_logos()) > 0) && (!isset($options['editable']) or $options['editable']),
		'objet' => $objet,
		'id_objet' => $id_objet,
		'_options' => $options,
	);

	// rechercher le logo de l'objet
	// la fonction prend un parametre '_id_objet' etrange :
	// le nom de la cle primaire (et non le nom de la table)
	// ou directement le nom du raccourcis a chercher
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$roles_logos = array_keys(lister_roles_logos($objet));

	foreach ($roles_logos as $role) {
		$logo = $chercher_logo($id_objet, $_id_objet, $role);
		if ($logo) {
			$res[$role] = $logo[0];
		}
	}

	// si le logo n'est pas editable et qu'il n'y en a pas, on affiche pas du tout le formulaire
	if (!$res['editable']
		and !isset($res['logo'])
		and !isset($res['logo_survol'])
	) {
		return false;
	}

	return $res;
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
	$erreurs = array();
	// verifier les extensions
	$sources = formulaire_editer_logo_get_sources($objet);
	foreach ($sources as $role => $file) {
		// seulement si une reception correcte a eu lieu
		if ($file and $file['error'] == 0) {
			// Utiliser $GLOBALS['formats_logos'] au lieu de cette liste
			// hardcodée ? Attention que 'jpeg' n'est pas dans la globale, à
			// compléter ?
			if (!in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), array('jpg', 'png', 'gif', 'jpeg'))) {
				$erreurs[$role] = _L('Extension non reconnue');
			}
		}
	}

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
	$res = array('editable' => ' ');

	// pas dans une boucle ? formulaire pour le logo du site
	// dans ce cas, il faut chercher un 'siteon0.ext'
	if (!$objet) {
		$objet = 'site';
	}

	include_spip('action/editer_logo');

	foreach (lister_roles_logos($objet) as $role => $options) {
		// effectuer la suppression si demandee d'un logo
		if (_request('supprimer_' . $role)) {
			logo_supprimer($objet, $id_objet, $role);
			$res['message_ok'] = ''; // pas besoin de message : la validation est visuelle
			set_request('logo_up', ' ');
		// remplacer par un autre document si demandé
		} elseif ($id_document = _request('document_mediatheque_' . $role . '_' . $objet . '_' . $id_objet)) {
			logo_modifier_document($objet, $id_objet, $role, intval($id_document));
			$res['message_ok'] = ''; // pas besoin de message : la validation est visuelle
		}
	}

	// Sinon remplacer les logos par le ou les éventuels logos uploadés
	foreach (formulaire_editer_logo_get_sources($objet) as $role => $file) {
		if ($file and $file['error'] == 0) {
			if ($err = logo_modifier($objet, $id_objet, $role, $file)) {
				$res['message_erreur'] = $err;
			} else {
				$res['message_ok'] = '';
			} // pas besoin de message : la validation est visuelle
			set_request('logo_up', ' ');
		} elseif ($file and in_array($file['error'], array(UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE))) {
			$res['message_erreur'] = 'fichier trop volumineux';
		} elseif ($file and in_array($file['error'], array(UPLOAD_ERR_PARTIAL,UPLOAD_ERR_NO_FILE))) {
			$res['message_erreur'] = 'upload incomplet';
		} elseif ($file and ($file['error'] > 5)) {
			$res['message_erreur'] = 'erreur serveur';
		}
	}

	// on vide les valeurs postées dans les rôles pour qu'elles soit recalculées
	// pendant le prochain appel à la fonction charger
	foreach (lister_roles_logos($objet) as $role => $options) {
		set_request($role, null);
	}

	// Invalider les caches de l'objet
	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id_objet'");

	if ($retour) {
		$res['redirect'] = $retour;
	}

	return $res;
}


/**
 * Extraction des sources des fichiers uploadés correspondant aux logos
 * si leur upload s'est bien passé.
 *
 * @param string $objet : le nom d'un objet, permet de ne sortir que les
 *                        fichiers correspondant aux rôles possibles pour un
 *                        type d'objet donné.
 *
 * @return array Sources des fichiers dans les clés données par des noms de
 *     rôles, comme « logo » ou « logo_survol »
 */
function formulaire_editer_logo_get_sources($objet = null) {
	if (!$_FILES) {
		$_FILES = isset($GLOBALS['HTTP_POST_FILES']) ? $GLOBALS['HTTP_POST_FILES'] : array();
	}
	if (!is_array($_FILES)) {
		return array();
	}

	$sources = array();
	foreach (lister_roles_logos($objet) as $role => $options) {
		if (isset($_FILES[$role])) {
			$sources[$role] = $_FILES[$role];
		}
	}

	return $sources;
}
