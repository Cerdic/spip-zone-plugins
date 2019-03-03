<?php
/**
 * Gestion du formulaire de d'édition de restriction
 *
 * @plugin     Locations d&#039;objets - restrictions
 * @copyright  2019
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Locations_objets_restrictions\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_restriction
 *     Identifiant du restriction. 'new' pour un nouveau restriction.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le restriction créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un restriction source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du restriction, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_restriction_identifier_dist($id_restriction = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_restriction), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de restriction
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_restriction
 *     Identifiant du restriction. 'new' pour un nouveau restriction.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le restriction créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un restriction source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du restriction, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_restriction_charger_dist($id_restriction = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	include_spip('inc/locations_objets_restrictions');
	include_spip('inc/saisies');
	$valeurs = formulaires_editer_objet_charger('restriction', $id_restriction, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	$restrictions = chercher_definitions_restrictions();
	$valeurs['_types_restriction'] = [];

	foreach ($restrictions as $type => $restriction) {
		$valeurs['_types_restriction'][$type] = $restriction['nom'];
	}

	$type_restriction = _request('type_restriction') ?
		_request('type_restriction') :
		$valeurs['type_restriction'];


	if (count($valeurs['_types_restriction']) <= 1) {
		$type_restriction = $valeurs['type_restriction'] = $type;
	}
	else {
		$valeurs['type_restriction'] = $type_restriction;
	}

	$valeurs['_valeurs_restriction'] = json_decode($valeurs['valeurs_restriction'], TRUE);

	if ($type_restriction) {
		foreach (saisies_lister_par_nom($restrictions[$type_restriction]['saisies']) AS $nom => $saisie) {
			$valeurs[$nom] = _request($nom) ?
				_request($nom) :
				$valeurs['_valeurs_restriction'][$nom];
		}
	}

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de restriction
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_restriction
 *     Identifiant du restriction. 'new' pour un nouveau restriction.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le restriction créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un restriction source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du restriction, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_restriction_verifier_dist($id_restriction = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	include_spip('public/assembler');
	include_spip('inc/saisies');
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('restriction', $id_restriction, array('titre'));

// Préparer les données multis pour l'enregistrement.
	if (!$erreurs) {
		$function = charger_fonction(_request('type_restriction'), "restrictions", true);
		$saisies = $function(calculer_contexte());
		$valeurs_restriction = saisies_lister_par_nom(
			array(
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => 'specifique',
					),
					'saisies' => $saisies['saisies']
				)
			));

		$restriction = array();
		foreach ($valeurs_restriction as $champ) {
			if ($request = _request($champ['options']['nom'])) {
				$restriction[$champ['options']['nom']] = $request;
			}
		}
		set_request('valeurs_restriction', json_encode($restriction));
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de restriction
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_restriction
 *     Identifiant du restriction. 'new' pour un nouveau restriction.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le restriction créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un restriction source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du restriction, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_restriction_traiter_dist($id_restriction = 'new', $retour = '', $associer_objet = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$retours = formulaires_editer_objet_traiter('restriction', $id_restriction, '', $lier_trad, $retour, $config_fonc, $row, $hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet and $id_restriction = $retours['id_restriction']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet and $id_objet and autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');

			objet_associer(array('restriction' => $id_restriction), array($objet => $id_objet));

			if (isset($retours['redirect'])) {
				$retours['redirect'] = parametre_url($retours['redirect'], 'id_lien_ajoute', $id_restriction, '&');
			}
		}
	}

	return $retours;
}
