<?php
/**
 * Gestion du formulaire de d'édition de reservation
 *
 * @plugin	 Réservation évènements
 * @copyright  2013
 * @author	 Rainer Müller
 * @licence	GNU/GPL
 * @package	SPIP\Reservation_evenement\Formulaires
 */
if (! defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_reservation
 *        	Identifiant du reservation. 'new' pour un nouveau reservation.
 * @param string $retour
 *        	URL de redirection après le traitement
 * @param int $lier_trad
 *        	Identifiant éventuel d'un reservation source d'une traduction
 * @param string $config_fonc
 *        	Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *        	Valeurs de la ligne SQL du reservation, si connu
 * @param string $hidden
 *        	Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string Hash du formulaire
 */
function formulaires_editer_reservation_identifier_dist($id_reservation = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(
		intval($id_reservation)
	));
}

/**
 * Chargement du formulaire d'édition de reservation
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_reservation
 *        	Identifiant du reservation. 'new' pour un nouveau reservation.
 * @param string $retour
 *        	URL de redirection aprês le traitement
 * @param int $lier_trad
 *        	Identifiant éventuel d'un reservation source d'une traduction
 * @param string $config_fonc
 *        	Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *        	Valeurs de la ligne SQL du reservation, si connu
 * @param string $hidden
 *        	Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array Environnement du formulaire
 */
function formulaires_editer_reservation_charger_dist($id_reservation = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('reservation', $id_reservation, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	if (isset($valeurs['langue']))
		$valeurs['lang'] = $valeurs['langue'];
	if (isset($valeurs['reference']) and ! $valeurs['reference']) {
		$fonction_reference = charger_fonction('reservation_reference', 'inc/');
		$valeurs['reference'] = $fonction_reference();
	}

	$valeurs['quantite'] = _request('quantite') ? _request('quantite') : 1;

	// les champs extras auteur
	include_spip('cextras_pipelines');

	if (function_exists('champs_extras_objet')) {
		// Charger les valeurs extras
		//Les auteurs
		$valeurs['champs_extras_auteurs'] = champs_extras_objet(table_objet_sql('auteur'));
		foreach ($valeurs['champs_extras_auteurs'] as $key => $value) {
			$donnees_auteur = unserialize($valeurs['donnees_auteur']);

			$valeurs[$value['options']['nom']] = isset($donnees_auteur[$value['options']['nom']]) ? $donnees_auteur[$value['options']['nom']] : $donnees_auteur[$value['options']['label']];
			$valeurs['champs_extras_auteurs'][$key]['options']['label'] = extraire_multi($value['options']['label']);
		}
	}

	$valeurs['_hidden'] .= '<input type="hidden" name="quantite" value="' . $valeurs['quantite'] . '"/>';

	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de reservation
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_reservation
 *        	Identifiant du reservation. 'new' pour un nouveau reservation.
 * @param string $retour
 *        	URL de redirection après le traitement
 * @param int $lier_trad
 *        	Identifiant éventuel d'un reservation source d'une traduction
 * @param string $config_fonc
 *        	Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *        	Valeurs de la ligne SQL du reservation, si connu
 * @param string $hidden
 *        	Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array Tableau des erreurs
 */
function formulaires_editer_reservation_verifier_dist($id_reservation = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$email = trim(_request('email'));
	$obligatoire = array(
		'reference'
	);
	$enregistrer_compte = TRUE;
	if (! _request('id_auteur') and (_request('nom') or $email)) {
		$obligatoire = Array_merge($obligatoire, array(
			'nom',
			'email'
		));
		$enregistrer_compte = FALSE;
	}
	else {
		$obligatoire = array_merge($obligatoire, array(
			'id_auteur'
		));
		set_request('email', '');
		set_request('nom', '');
	}


	$erreurs = formulaires_editer_objet_verifier('reservation', $id_reservation, $obligatoire);
	if (in_array('email', $obligatoire) and $email) {
		$email_reutilisable = lire_config('reservation_evenement/email_reutilisable', '');
		include_spip('inc/filtres');
		// un redacteur qui modifie son email n'a pas le droit de le vider si il y en avait un
		if (!email_valide($email)) {
			$id_auteur_session = isset($GLOBALS['visiteur_session']['id_auteur']) ? $GLOBALS['visiteur_session']['id_auteur'] : '';
			$erreurs['email'] = (($id_auteur == $id_auteur_session) ? _T('form_email_non_valide') : _T('form_prop_indiquer_email'));
		}
		elseif (!$id_auteur and !$email_reutilisable) {
			if ($email_utilise = sql_getfetsel('email', 'spip_auteurs', 'email=' . sql_quote($email)))
				$erreurs['email'] = _T('reservation:erreur_email_utilise');
		}
	}

	// verifier et changer en datetime sql la date envoyee
	$verifier = charger_fonction('verifier', 'inc');
	$champ = 'date_paiement';
	$normaliser = null;
	if ($erreur = $verifier(_request($champ), 'date', array(
		'normaliser' => 'datetime'
	), $normaliser)) {
		$erreurs[$champ] = $erreur;
		// si une valeur de normalisation a ete transmis, la prendre.
	}
	elseif (! is_null($normaliser) and _request($champ)) {
		set_request($champ, $normaliser);
	}
	else
		set_request($champ, '0000-00-00 00:00:00');

	if (! $enregistrer_compte) {
		// les champs extras auteur
		include_spip('cextras_pipelines');

		if (function_exists('champs_extras_objet')) {
			include_spip('inc/saisies');
			// Charger les définitions
			$champs_extras_auteurs = champs_extras_objet(table_objet_sql('auteur'));
			$erreurs = array_merge($erreurs, saisies_verifier($champs_extras_auteurs));
		}
	}
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de reservation
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_reservation
 *        	Identifiant du reservation. 'new' pour un nouveau reservation.
 * @param string $retour
 *        	URL de redirection après le traitement
 * @param int $lier_trad
 *        	Identifiant éventuel d'un reservation source d'une traduction
 * @param string $config_fonc
 *        	Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *        	Valeurs de la ligne SQL du reservation, si connu
 * @param string $hidden
 *        	Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array Retours des traitements
 */
function formulaires_editer_reservation_traiter_dist($id_reservation = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return formulaires_editer_objet_traiter('reservation', $id_reservation, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
}

?>