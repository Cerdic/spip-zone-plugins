<?php
/**
 * Gestion du formulaire de modification de la raison d'archivage ou de désarchivage.
 *
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de modification de la raison d'archivage ou de désarchivage.
 *
 * @param string $objet
 * @param int    $id_objet
 * @param mixed  $redirect
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
	// -- récupération de l'état d'archivage
	include_spip('inc/archobjet');
	$etat_archivage = archivage_lire_etat_objet(
		$objet,
		$id_objet
	);

	// -- constitution de la liste des raisons en fonction du type d'objet et de l'état d'archivage
	$raisons = archivage_lister_raisons($objet, $etat_archivage['etat']);

	// Constitution du tableau des variables du formulaire.
	$valeurs = array(
		'editable'       => $editable,
		'_raison_label'  => _T('archobjet:edition_raison_label'),
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
 * @param mixed  $redirect
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

		// Fermeture de la modale et redirection
		$autoclose = '<script type="text/javascript">if (window.jQuery) jQuery.modalboxclose();</script>';
		$retour['message_ok'] = _T('info_modification_enregistree') . $autoclose;
		$retour['redirect'] = $redirect ? $redirect : '';
	} else {
		$retour['message_erreur'] = _T('archobjet:erreur_modifier_archivage_non_autorisee');
	}

	return $retour;
}
