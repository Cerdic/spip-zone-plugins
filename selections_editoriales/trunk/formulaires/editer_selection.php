<?php
/**
 * Gestion du formulaire de d'édition de selection
 *
 * @plugin     Sélections éditoriales
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Selections_editoriales\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_selection
 *     Identifiant du selection. 'new' pour un nouveau selection.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier le mot créé à cet objet,
 *     tel que 'article|3'
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du selection, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_selection_identifier_dist($id_selection = 'new', $retour = '', $associer_objet = '', $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_selection)));
}

/**
 * Chargement du formulaire d'édition de selection
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_selection
 *     Identifiant du selection. 'new' pour un nouveau selection.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier le mot créé à cet objet,
 *     tel que 'article|3'
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du selection, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_selection_charger_dist($id_selection = 'new', $retour = '', $associer_objet = '', $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('selection', $id_selection, '', '', $retour, $config_fonc, $row, $hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de selection
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_selection
 *     Identifiant du selection. 'new' pour un nouveau selection.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier le mot créé à cet objet,
 *     tel que 'article|3'
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du selection, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_selection_verifier_dist($id_selection = 'new', $retour = '', $associer_objet = '', $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = formulaires_editer_objet_verifier('selection', $id_selection, array('titre'));

	// L'identifiant doit être unique s'il existe
	if (
		$identifiant = _request('identifiant')
		and $id = intval(sql_getfetsel('id_selection', 'spip_selections', 'identifiant = '.sql_quote($identifiant)))
		and $id_selection != $id
		and include_spip('inc/filtres')
		and $titre_selection = generer_info_entite($id, 'selection', 'titre')
		and $url_selection = generer_info_entite($id, 'selection', 'url')
	) {
		$erreurs['identifiant'] = _T('selection:erreur_identifiant_existant', array('selection' => "<a href=\"$url_selection\">$titre_selection</a>"));
	}

	// Vérifier que la limite est bien un entier
	if (
		$limite = _request('limite')
		and (
			!is_numeric($limite) // pas un nombre
			or $limite != intval($limite) // pas un entier
			or $limite <= 0 // pas positif
		)
	) {
		$erreurs['limite'] = _T('selection:erreur_limite_entier');
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de selection
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_selection
 *     Identifiant du selection. 'new' pour un nouveau selection.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel 'objet|x' indiquant de lier le mot créé à cet objet,
 *     tel que 'article|3'
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du selection, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_selection_traiter_dist($id_selection = 'new', $retour = '', $associer_objet = '', $config_fonc = '', $row = array(), $hidden = '') {
	$res = formulaires_editer_objet_traiter('selection', $id_selection, '', '', $retour, $config_fonc, $row, $hidden);
	if ($associer_objet) {
		if (intval($associer_objet)) {
			// compat avec l'appel de la forme ajouter_id_article
			$objet = 'article';
			$id_objet = intval($associer_objet);
		} else {
			list($objet, $id_objet) = explode('|', $associer_objet);
		}
		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_selection');
			selection_associer($res['id_selection'], array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url($res['redirect'], 'id_lien_ajoute', $res['id_selection'], '&');
			}
		}
	}
	return $res;
}
