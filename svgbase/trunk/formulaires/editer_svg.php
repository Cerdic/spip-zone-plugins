<?php
/**
 * Gestion du formulaire de d'édition de svg
 *
 * @plugin     SVG en base de données
 * @copyright  2019
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\Svgbase\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_svg
 *     Identifiant du svg. 'new' pour un nouveau svg.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le svg créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un svg source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du svg, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_svg_identifier_dist($id_svg = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_svg), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de svg
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_svg
 *     Identifiant du svg. 'new' pour un nouveau svg.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le svg créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un svg source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du svg, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_svg_charger_dist($id_svg = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('svg', $id_svg, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de svg
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_svg
 *     Identifiant du svg. 'new' pour un nouveau svg.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le svg créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un svg source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du svg, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_svg_verifier_dist($id_svg = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('svg', $id_svg, array('titre'));

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de svg
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_svg
 *     Identifiant du svg. 'new' pour un nouveau svg.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le svg créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un svg source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du svg, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_svg_traiter_dist($id_svg = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$retours = formulaires_editer_objet_traiter('svg', $id_svg, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet and $id_svg = $retours['id_svg']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			
			objet_associer(array('svg' => $id_svg), array($objet => $id_objet));
			
			if (isset($retours['redirect'])) {
				$retours['redirect'] = parametre_url($retours['redirect'], 'id_lien_ajoute', $id_svg, '&');
			}
		}
	}
	$fichier = _request('fichier');
	if ($fichier != '') {
		$svg = file_get_contents($fichier);
		sql_updateq('spip_svg', array('svg' => $svg), "id_svg=".intval($id_svg));
	}
	return $retours;
}
