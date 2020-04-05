<?php

/**
 * Fichier d'une tâche de fond du plugin 'Déréférencer les médias'.
 *
 * @plugin     Déréférencer les médias
 *
 * @copyright  2015-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('medias_dereferencer_fonctions');

function genie_medias_dereferencer_vu_dist($t) {
	include_spip('inc/config');
	$lier_document_cfg = lire_config('medias_dereferencer/lier_document');
	/**
	 * Il faut avoir activé la liaison des documents dans le formulaire de configuration.
	 * Si on n'a pas encore configuré le plugin, on n'active pas la liaison des documents.
	 */
	if (!empty($lier_document_cfg) and $lier_document_cfg == 'oui') {
		include_spip('inc/session');
		$message_log = array();
		$message_log[] = "\n-----";
		$message_log[] = date_format(date_create(), 'Y-m-d H:i:s');
		$message_log[] = 'Fonction : ' . __FUNCTION__;
		if (session_get('id_auteur')) {
			$message_log[] = "L'action a été lancé par l'auteur #" . session_get('id_auteur') . ', ' . session_get('nom') . ' (' . session_get('statut') . ')';
		} else {
			$message_log[] = "L'action a été lancé par SPIP en tâche de fond.";
		}

		medias_maj_documents_non_lies();

		// on met l'heure de fin de la procédure dans le message de log
		$message_log[] = date_format(date_create(), 'Y-m-d H:i:s');
		$message_log[] = "-----\n";
		// Et maintenant on stocke les messages dans un fichier de log.
		include_spip('inc/utils');
		spip_log(implode("\n", $message_log), 'medias_dereferencer');
	}

	return true;
}
