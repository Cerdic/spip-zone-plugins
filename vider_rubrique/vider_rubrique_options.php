<?php
/***************************************************************************\
 * Plugin Vider Rubrique
 * Licence GPL (c) 2012-2018 - Apsulis
 * Suppression de tout le contenu d'une rubrique
 *
 * \***************************************************************************/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function vider_rubrique_objet_poubelle($objet, $id_objet, $statut) {
	spip_log("Suppression $objet : $id_objet.", 'vider_rubrique');
	$c = array('statut' => $statut);

	include_spip('action/editer_objet');
	include_spip('inc/config');
	if ($err = objet_instituer($objet, $id_objet, $c)) {
		$res = array('message_erreur' => $err, 'objet' => $objet);
	} else {
		$res = array('message_ok' => _T('info_modification_enregistree'));
	}
	if (lire_config("vider_rubrique/config/effacement") == "oui") {
		supprimer_les_logos($objet, $id_objet);
	}

	return $res;
}

function supprimer_rubrique($liste_id) {
	include_spip('inc/utils');
	include_spip('base/abstract_sql');
	spip_log(print_r($liste_id, true), 'vider_rubrique');
	$supprimer_rubrique = charger_fonction('supprimer_rubrique', 'action');
	/* On efface les rubriques les plus profondes en premier, sinon on ne pourra pas supprimer ses parents */
	$les_id = array_reverse(explode(",", $liste_id));
	foreach ($les_id as $key => $value) {
		$supprimer_rubrique($value);
		supprimer_les_logos("rubrique", $value);
		spip_log("Suppression de la rubrique : $value.", 'vider_rubrique');
	}
	include_spip('inc/rubriques');
	calculer_rubriques();

	return true;
}

function supprimer_les_logos($type, $id_objet) {
	supprimer_logo($type, $id_objet);
	supprimer_logo($type, $id_objet, 'off');
}

function supprimer_logo($type, $id_objet, $logo_type = 'on') {
	include_spip('inc/utils');
	include_spip('inc/flock');
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$le_logo = $chercher_logo($id_objet, 'id_' . $type, $logo_type);
	$le_logo = (isset($le_logo[0]) ? $le_logo[0] : 'empty');
	if (!file_exists($le_logo)) {
		return false;
	} else {
		// Un message de log que si le logo existe et donc, qu'il peut être supprimé.
		spip_log("Suppression du logo : $le_logo", 'vider_rubrique');
		spip_unlink($le_logo);
	}
}

function vider_rubrique_dissocier_document($liste_id) {
	if (empty($liste_id) or is_null($liste_id)) {
		return false;
	}
	include_spip('base/abstract_sql');

	// Dissocier les documents des rubriques.
	sql_delete('spip_documents_liens', "objet='rubrique' AND id_objet IN ($liste_id)");
	spip_log("Suppression des liens de la rubrique #$liste_id avec les documents.", 'vider_rubrique');


}