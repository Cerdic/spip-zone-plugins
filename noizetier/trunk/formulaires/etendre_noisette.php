<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('autoriser')) {
	include_spip('inc/autoriser');
}     // si on utilise le formulaire dans le public


/**
 * Formulaire listant les pages compatibles avec la noisette passée en argument et pouvant donc
 * recevoir cette même noisette dans le bloc fourni en argument.
 *
 * @param int    $noisette
 *        Tableau des informations nécessaires sur la noisette à copier, à savoir, l'id et le type de noisette.
 * @param string $bloc
 * 		  Bloc de page au sens Z.
 * @param string $redirect
 * 		URL de redirection : on revient sur la page d'origine de l'action.
 *
 * @return array
 * 		Tableau des champs postés pour l'affichage du formulaire.
 */
function formulaires_etendre_noisette_charger_dist($noisette, $bloc, $redirect) {

	// Initialisation
	$valeurs = array();

	// On récupère le type de page et la composition associé au type de noisette
	$select = array('type', 'composition');
	$where = array('type_noisette=' . sql_quote($noisette['type_noisette']));
	$type_noisette = sql_fetsel($select, 'spip_types_noisettes', $where);

	// On cherche la liste des pages:
	// - compatibles avec la noisette
	// - dont le bloc concerné est bien configurable
	// - pouvant être configurées par l'utilisateur
	$select = array('nom', 'page', 'blocs_exclus');
	$where = array();
	if (!empty($type_noisette['type'])) {
		$where[] = 'type=' . sql_quote($type_noisette['type']);
	}
	if (!empty($type_noisette['composition'])) {
		$where[] = 'composition=' . sql_quote($type_noisette['composition']);
	}
	$pages = sql_allfetsel($select, 'spip_noizetier_pages', $where);
	$valeurs['_pages'] = array();
	if ($pages) {
		foreach ($pages as $_page) {
			if ((!in_array($bloc, unserialize($_page['blocs_exclus'])))
			and autoriser('configurerpage', 'noizetier', 0, '', array('page' => $_page['page']))) {
				$valeurs['_pages'][$_page['page']] = _T_ou_typo($_page['nom']) . " (<em>{$_page['page']}</em>)";
			}
		}
	}

	if ($valeurs['_pages']) {
		$valeurs['editable'] = true;
	} else {
		$valeurs['editable'] = false;
	}

	return $valeurs;
}

function formulaires_etendre_noisette_verifier_dist($noisette, $bloc, $redirect) {

	$erreurs = array();
	if (!_request('pages')) {
		$erreurs['pages'] = _T('noizetier:erreur_aucune_page_selectionnee');
	}

	return $erreurs;
}


function formulaires_etendre_noisette_traiter_dist($noisette, $bloc, $redirect) {

	$retour = array();

	// Récupération des pages sélectionnées.
	$pages = _request('pages');

	// Pour chaque page on copie la noisette avec tous ses paramètres.
	// Il est inutile de tester l'autorisation sur la page car cela a déjà été fait lors du chargement.
	include_spip('inc/ncore_noisette');
	$erreurs = array();
	$conteneur = array();
	foreach ($pages as $_page) {
		// Définir le conteneur de la noisette, à savoir, le squelette du bloc de la page concernée.
		$conteneur['squelette'] = "${bloc}/${_page}";
		if (!$id_noisette = noisette_ajouter('noizetier', $noisette['type_noisette'], $conteneur)) {
			$erreurs[] = $_page;
		} else {
			// Mettre à jour les informations spécifiques de la noisette source si demandé.
			static $valeurs = null;
			if ($valeurs === null) {
				// On construit une seule fois le tableau des modifications si il y en a et on l'utilise
				// pour chaque page.
				$valeurs = array();
				foreach (array('copie_parametres', 'copie_balise', 'copie_css') as $_champ) {
					if (_request($_champ)) {
						$champ_noisette = str_replace('copie_', '', $_champ);
						$valeurs[$champ_noisette] = $noisette[$champ_noisette];
					};
				}
			}
			if ($valeurs) {
				// Mise à jour de la noisette créée avec les champs demandés.
				noisette_parametrer('noizetier', intval($id_noisette), $valeurs);
			}
		}
	}

	if (!$erreurs) {
		$retour['message_ok'] = _T('info_modification_enregistree');
		$retour['redirect'] = $redirect;
	} else {
		$retour['message_erreur'] =
			_T('noizetier:erreur_etendre_noisette', array('pages', implode(', ', $erreurs)));
	}

	return $retour;
}
