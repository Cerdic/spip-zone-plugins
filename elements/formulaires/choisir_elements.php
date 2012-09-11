<?php

/**
 * Gestion des éléments choisis sur un objet / id_objet, pour un bloc donné
 *
 * @package SPIP\Elements\Formulaires
**/

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement du formulaire de choix d'éléments.
 * 
 * Affiche le éléments choisis et leur configuration et permet
 * de les ordonner, de les supprimer ou d'ajouter de nouveaux éléments
 *
 * @param string $objet
 *     Type d'objet réceptionnant les éléments
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param string $bloc
 *     Nom du bloc qui utilisera les éléments
 * 
 * @return array
 *     Contexte envoyé au squelette HTML du formulaire.
**/
function formulaires_choisir_elements_charger($objet, $id_objet, $bloc='extra') {
	if (!$objet OR !$id_objet OR !$bloc) {
		return null;
	}

	$contexte = array();

	// elements déjà là
	$table = table_objet_sql($objet);
	$choix = sql_getfetsel('elements', 'spip_elements', array(
		'objet=' . sql_quote($objet),
		'id_objet=' . sql_quote($id_objet),
		'bloc=' . sql_quote($bloc)
	));
	if (!$choix) $choix = array();
	else $choix = unserialize($choix);

	$contexte['elements'] = $choix;

	return $contexte;
}


/**
 * Vérification du formulaire de choix d'éléments.
 * 
 * Pour chaque champ posté, effectue les vérifications demandées par
 * les saisies et retourne éventuellement les erreurs de saisie.
 *
 * @param string $objet
 *     Type d'objet réceptionnant les éléments
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param string $bloc
 *     Nom du bloc qui utilisera les éléments
 * 
 * @return array
 *     Tableau des erreurs
**/
function formulaires_choisir_elements_verifier($objet, $id_objet, $bloc='extra') {
	$erreurs = array();

	// tous les éléments sélectionnés
	$elements = _request('elements');
	if (!$elements) $elements = array();

	// ajout d'un élément à la liste
	if ($elem = _request('ajouter_element')) {

		if ($desc = lister_elements_par_nom($elem)) {
			// indiquer qu'on a pris en compte l'element
			$erreurs['message_info'] = _T("elements:message_element_plus")
				. '<br />' . _T("elements:message_element_enregistrer");

			// ajout de l'élément aux éléments sélectionnés
			$elements[ count($elements) ] = array(
				'element' => $elem,   // nom de l'élément
				'contexte' => array() // contexte qui sera transmis au squelette de l'élément
			);
			// on les fiche dans l'environnement...
			set_request('elements', $elements);
		} else {
			// bah c'est une drole d'erreur !
			$erreurs['message_erreur'] = _T("elements:element_type_introuvable");
		}
	}

	// supprimer un élément de la liste
	if ($elem = _request('supprimer_element')) {
		// element/3
		list(,$index) = explode('/', $elem);
		unset($elements[$index]);
		$elements = array_values($elements);
		// on les fiche dans l'environnement...
		set_request('elements', $elements);
		// indiquer qu'on a pris en compte l'element
		$erreurs['message_info'] = _T("elements:message_element_moins")
			. '<br />' . _T("elements:message_element_enregistrer");
	}

	// déplace un élément d'un cran vers le bas ou vers le haut
	if ($elem = _request('deplacer_element')) {
		// element/3/-1
		list(,$index,$pos) = explode('/', $elem);
		$deplace = $elements[$index];
		unset($elements[$index]);
		$elements = array_values($elements);
		// nouvel index
		if ($pos[0] == '-') {
			$index = $index - substr($pos,1);
			if ($index < 0) $index = 0;
		} else {
			$index = $index + $pos;
			if ($index > count($elements)) $index = count($elements);
		}

		array_splice($elements, $index, 0, array($deplace));

		// on les fiche dans l'environnement...
		set_request('elements', $elements);
		// indiquer qu'on a pris en compte l'element
		$erreurs['message_info'] = _T("elements:message_element_deplace")
			. '<br />' . _T("elements:message_element_enregistrer");
	}

	return $erreurs;
}



/**
 * Traitements du formulaire de choix d'éléments.
 * 
 * Pour chaque champ posté, effectue les vérifications demandées par
 * les saisies et retourne éventuellement les erreurs de saisie.
 *
 * @param string $objet
 *     Type d'objet réceptionnant les éléments
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param string $bloc
 *     Nom du bloc qui utilisera les éléments
 * 
 * @return array
 *     Tableau des erreurs
**/
function formulaires_choisir_elements_traiter($objet, $id_objet, $bloc='extra') {
	$res = array();
	$res['editable'] = true;

	$elements = _request('elements');
	$elements = array_values($elements); // recalculer les index (sait-on jamais)

	// pas d'élément, on supprime la ligne SQL
	if (!$elements) {
		sql_delete('spip_elements', array(
			'objet=' . sql_quote($objet),
			'id_objet=' . sql_quote($id_objet),
			'bloc=' . sql_quote($bloc)
			));
	} else {
		$elements = serialize($elements);

		$old = sql_getfetsel('id_element', 'spip_elements', array(
			'objet=' . sql_quote($objet),
			'id_objet=' . sql_quote($id_objet),
			'bloc=' . sql_quote($bloc)
		));

		if ($old) {
			sql_updateq('spip_elements', array('elements' => $elements),
			    array('id_element='. sql_quote($old)));
		} else {
			sql_insertq('spip_elements', array(
				'objet'    => $objet,
				'id_objet' => $id_objet,
				'bloc'     => $bloc,
				'elements' => $elements
			));
		}
	}
	$res['message_ok'] = _T("elements:elements_sauvegardes");

	return $res;
}


/**
 * Créer les saisies de configuration d'un élément
 *
 * - Ajoute un hidden du nom de l'élément
 * - Définit les name des saisies
 *
 * Change les noms de saisies tel que 'bidule' en 
 * @param array $element
 *     Tableau de descriptions l'un élément
 * @param int $index
 *     Numéro de l'élément dans la liste
 * @return array
 *     Tableau de descriptions des saisies
**/
function elements_saisies_preparer($element, $index) {

	$saisies = $element['parametres'];
	$saisies = saisies_transformer_noms($saisies, '~^(.*)$~', 'elements['.$index.'][contexte][$1]'); 
	$saisies = saisies_inserer($saisies, array(
		'saisie' => 'hidden',
		'options' => array(
			'nom' => "elements[$index][element]",
			'valeur' => $element['element'],
			'defaut' => $element['element'],
		)
	));

	return $saisies;
}
