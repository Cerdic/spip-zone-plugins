<?php
/**
 * Gestion du formulaire de modification de la raison d'archivage ou de désarchivage.
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de modification de la raison d'archivage ou de désarchivage.
 *
 * @param string $objet
 * @param int    $id_objet
 *
 * @return array
 */
function formulaires_editer_raison_archivage_charger($objet, $id_objet, $redirect) {

	// L'éditabilité :) est définie par un test permanent (par exemple "associermots") ET le 4ème argument
	include_spip('inc/autoriser');
	$editable = autoriser(
		'modifierarchivage',
		$objet,
		$id_objet,
		'',
		array('action' => 'modifier_raison')
	);

	// Construction de la liste des raisons
	// Récupération de l'état d'archivage
	include_spip('inc/archobjet_objet');
	$etat_archivage = objet_etat_archivage(
		$objet,
		$id_objet
	);

	// Construction de la liste des raisons
	// -- Initialisation de la liste par des raisons standard valables pour tous les types d'objets
	//    et pour l'état courant de l'objet. Ces raisons sont fournies par le plugin Archive.
	$etat = $etat_archivage['etat'];
	$ids_raisons = array(
		"${etat}_aucune",
		"${etat}_defaut"
	);
	// -- Ajout des raisons additionnelles fournies par d'autres plugins pour le type d'objet en question.
	$ids_raisons = pipeline(
		'liste_raison_archivage',
		array(
			'args' => array(
				'objet' => $objet,
				'etat' => $etat
			),
			'data' => $ids_raisons,
		)
	);
	// -- Calcul du tableau des raisons pour la saisie
	foreach ($ids_raisons as $_id_raison) {
		// La valeur aucune raison est en fait la chaine vide
		if ($_id_raison == "${etat}_aucune") {
			$raisons[''] = _T("archobjet:raison_${_id_raison}_label");
		} else {
			$raisons[$_id_raison] = _T("archobjet:raison_${_id_raison}_label");
		}
	}

	// Constitution du tableau des variables du formulaire.
	$valeurs = array(
		'editable'       => $editable,
		'_raison_label'  => _T("archobjet:edition_raison_label"),
		'_raisons'       => $raisons,
		'raison'         => $etat_archivage['raison_archive']
	);

	return $valeurs;
}

/**
 * Traiter le post des informations d'édition de liens.
 *
 * @param string $objet
 * @param int    $id_objet
 *
 * @return array
 */
function formulaires_editer_raison_archivage_traiter($objet, $id_objet, $redirect) {

	// Initialisation du retour
	$retour = array();

	if (
		include_spip('inc/autoriser')
		or autoriser('modifierarchivage', $objet, $id_objet, '', array('action' => 'modifier_raison'))
	) {
		// Acquérir la raison choisie et mise à jour en utilisant l'action de modification idoine.
		$raison = _request('raison');
		if ($modifier = charger_fonction('objet_modifier_archivage', 'action', true)) {
			$modifier("modifier_raison:${objet}:${id_objet}:${raison}");
		}

		// Fermeture de la modale
		$autoclose = '<script type="text/javascript">if (window.jQuery) jQuery.modalboxclose();</script>';
		$retour['message_ok'] = _T('info_modification_enregistree') . $autoclose;

		if ($redirect) {
			$retour['redirect'] = $redirect;
		}
	} else {
		$retour['message_erreur'] = _T('probleme_droits');
	}

	return $retour;
}
