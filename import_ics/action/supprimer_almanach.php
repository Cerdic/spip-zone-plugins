<?php
#tout est pompé du tutoriel de marcimat "chat"
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_almanach_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_supprimer_almanach_dist $arg pas compris");
	} else {
		action_supprimer_almanach_post($r[1]);
	}
}

function action_supprimer_almanach_post($id_almanach) {
	//recuperer tous les evenemments lies à l'almanach en cours
	$all = sql_allfetsel('id_objet', 'spip_almanachs_liens','id_almanach='.intval($id_almanach));
	//pour chacun d'entre eux supprimer l'entree correspondante dans la table evenement
	foreach ($all as $id_evenement_array) {
		$id_evenement=$id_evenement_array['id_objet'];
		sql_delete("spip_evenements","id_evenement=".intval($id_evenement));
	}
	//on supprime les entrees de la table de liaison
	sql_delete("spip_almanachs_liens","id_almanach=".intval($id_almanach));
	//on supprime l'almanach
	sql_delete("spip_almanachs", "id_almanach=" . intval($id_almanach));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_almanach/$id_almanach'");
}
?>
