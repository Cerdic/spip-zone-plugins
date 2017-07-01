<?php
/**
 * Gestion du formulaire de d'édition de associe_lien
 *
 * @plugin     Liens associés
 * @copyright  2017
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Liens_associes\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_associe_lien
 *     Identifiant du associe_lien. 'new' pour un nouveau associe_lien.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le associe_lien créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un associe_lien source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du associe_lien, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_associe_lien_identifier_dist($id_associe_lien = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_associe_lien), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de associe_lien
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_associe_lien
 *     Identifiant du associe_lien. 'new' pour un nouveau associe_lien.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le associe_lien créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un associe_lien source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du associe_lien, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_associe_lien_charger_dist($id_associe_lien= 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('associe_lien', $id_associe_lien, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	if ($id_associe_lien== 'oui') {
		$valeurs['_hidden'] .= '<input type="hidden" name="statut" value="publie"/>';
	}
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de associe_lien
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_associe_lien
 *     Identifiant du associe_lien. 'new' pour un nouveau associe_lien.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le associe_lien créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un associe_lien source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du associe_lien, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_associe_lien_verifier_dist($id_associe_lien = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('associe_lien', $id_associe_lien, array('titre'));

	if (_request('lien_interne')) {
		$obligatoires = array('objet_spip','id_objet_spip');
		foreach ($obligatoires as $champ) {
			if (!_request($champ)){
				$erreurs[$champ] = _T("info_obligatoire");
			}
		}
	}
	else {
		if($url = _request('url')) {
			$verifier = charger_fonction('verifier', 'inc/');
			if($erreur = $verifier($url , 'url', array(
				'mode' => 'php_filter',
				'type_protocole' => 'web'))) {
				$erreurs['url'] = $erreur;
			}
		}
		else {
			$erreurs['url'] = _T("info_obligatoire");
		}
	}
print_r($erreurs);
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de associe_lien
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_associe_lien
 *     Identifiant du associe_lien. 'new' pour un nouveau associe_lien.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le associe_lien créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un associe_lien source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du associe_lien, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_associe_lien_traiter_dist($id_associe_lien = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$retours = formulaires_editer_objet_traiter('associe_lien', $id_associe_lien, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet and $id_associe_lien = $retours['id_associe_lien']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');

			objet_associer(array('associe_lien' => $id_associe_lien), array($objet => $id_objet));

			if (isset($retours['redirect'])) {
				$retours['redirect'] = parametre_url($retours['redirect'], 'id_lien_ajoute', $id_associe_lien, '&');
			}
		}
	}

	return $retours;
}
