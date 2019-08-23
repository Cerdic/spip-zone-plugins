<?php
/**
 * Gestion du formulaire de d'édition de chapitre
 *
 * @plugin     Chapitres
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Chapitres\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_chapitre
 *     Identifiant du chapitre. 'new' pour un nouveau chapitre.
 * @param string $objet
 *     Type de l'objet parent racine
 * @param int $id_objet
 *     Identifiant de l'objet parent racine
 * @param int $id_parent
 *     Identifiant du chapitre parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un chapitre source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du chapitre, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_chapitre_identifier_dist($id_chapitre = 'new', $objet='', $id_objet=0, $id_parent=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_chapitre)));
}

/**
 * Déclarer les saisies des chapitres
 *
 * @param int|string $id_chapitre
 *     Identifiant du chapitre. 'new' pour un nouveau chapitre.
 * @param string $objet
 *     Type de l'objet parent racine
 * @param int $id_objet
 *     Identifiant de l'objet parent racine
 * @param int $id_parent
 *     Identifiant du chapitre parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un chapitre source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du chapitre, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_chapitre_saisies_dist($id_chapitre = 'new', $objet='', $id_objet=0, $id_parent=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	// S'il n'y a pas l'objet racine, il faut le trouver
	if (!$objet or !$id_objet) {
		// S'il y a un chapitre précis, normalement c'est renseigné
		if (
			(
				$id_source = intval($id_chapitre)
				or $id_source = intval($id_parent)
			)
			and $chapitre = sql_fetsel('objet, id_objet', 'spip_chapitres', 'id_chapitre = '.$id_source)
		) {
			$objet = $chapitre['objet'];
			$id_objet = intval($chapitre['id_objet']);
		}
	}
	
	$saisies = array(
		array(
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'id_chapitre',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('chapitre:champ_titre_label'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'chapitres',
			'options' => array(
				'nom' => 'id_parent',
				'label' => _T('chapitre:champ_id_parent_label'),
				'recursif' => 'oui',
				'objet' => $objet,
				'id_objet' => $id_objet,
				'id_parent' => 0,
				'exclus' => $id_chapitre,
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'chapo',
				'label' => _T('chapitre:champ_chapo_label'),
				'rows' => 4,
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'texte',
				'label' => _T('chapitre:champ_texte_label'),
				'rows' => 10,
			),
		),
	);
	
	return $saisies;
}

/**
 * Chargement du formulaire d'édition de chapitre
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_chapitre
 *     Identifiant du chapitre. 'new' pour un nouveau chapitre.
 * @param string $objet
 *     Type de l'objet parent racine
 * @param int $id_objet
 *     Identifiant de l'objet parent racine
 * @param int $id_parent
 *     Identifiant du chapitre parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un chapitre source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du chapitre, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_chapitre_charger_dist($id_chapitre = 'new', $objet='', $id_objet=0, $id_parent=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	// Autorisation
	include_spip('inc/autoriser');
	if (intval($id_chapitre) and !autoriser('modifier', 'chapitre', intval($id_chapitre))) {
		$valeurs = false;
	}
	elseif (!intval($id_chapitre)) {
		if ($objet and $id_objet and !autoriser('creerchapitredans', $objet, $id_objet)) {
			$valeurs = false;
		}
		elseif ($id_parent and !autoriser('creerchapitredans', 'chapitre', $id_parent)) {
			$valeurs = false;
		}
		else {
			$valeurs = formulaires_editer_objet_charger('chapitre', $id_chapitre, $id_parent, $lier_trad, $retour, $config_fonc, $row, $hidden);
		}
	}
	else {
		$valeurs = formulaires_editer_objet_charger('chapitre', $id_chapitre, $id_parent, $lier_trad, $retour, $config_fonc, $row, $hidden);
	}
	return $valeurs;
}

/**
 * Traitement du formulaire d'édition de chapitre
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_chapitre
 *     Identifiant du chapitre. 'new' pour un nouveau chapitre.
 * @param string $objet
 *     Type de l'objet parent racine
 * @param int $id_objet
 *     Identifiant de l'objet parent racine
 * @param int $id_parent
 *     Identifiant du chapitre parent
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un chapitre source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du chapitre, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_chapitre_traiter_dist($id_chapitre = 'new', $objet='', $id_objet=0, $id_parent=0, $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	// On force ces valeurs
	set_request('objet', $objet);
	set_request('id_objet', $id_objet);
	// id_parent : on prend en priorité celui choisi manuellement
	$id_parent = is_null(_request('id_parent')) ? $id_parent : intval(_request('id_parent'));

	$retours = formulaires_editer_objet_traiter('chapitre', $id_chapitre, $id_parent, $lier_trad, $retour, $config_fonc, $row, $hidden);

	return $retours;
}
