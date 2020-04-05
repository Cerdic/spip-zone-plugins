<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip("action/editer_liens");
include_spip("inc/autoriser");
function action_supprimer_evenements_almanach_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_evenements_almanach_dist $arg pas compris");
	} else {
		action_supprimer_evenements_almanach_post($r[1]);
	}
}

function action_supprimer_evenements_almanach_post($id_almanach) {
	spip_log ("Suppression des évènements de l'almanach $id_almanach","import_ics"._LOG_INFO);
	//recuperer tous les evenemments lies à l'almanach en cours
	$all = sql_allfetsel('id_objet', 'spip_almanachs_liens','id_almanach='.intval($id_almanach));
	//pour chacun d'entre eux supprimer l'entree correspondante dans la table evenement
	$les_evenements=array();
	foreach ($all as $id_evenement_array) {
		$id_evenement=$id_evenement_array['id_objet'];
		$les_evenements[]=$id_evenement;
		sql_delete("spip_evenements","id_evenement=".intval($id_evenement));
	}
	//on supprime les liaisons
	
	objet_dissocier(
		array("mot"=>"*","almanach"=>$id_almanach),
		array("evenement"=>$les_evenements)
	);
	
	include_spip('inc/invalideur');
	suivre_invalideur(1);
}
?>
