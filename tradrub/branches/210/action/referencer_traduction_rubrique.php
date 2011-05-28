<?php

/**
 * Permet de modifier l'identifiant du groupe de traduction,
 * c'est a dire de donner l'objet de reference.
 * 
**/
function action_referencer_traduction_rubrique_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	list($type, $id_ancienne_reference, $id_nouvelle_reference) = explode('/',$arg);
	if (!$type = objet_type($type)
	or !$id_ancienne_reference = intval($id_ancienne_reference)
	or !$id_nouvelle_reference = intval($id_nouvelle_reference)) {
		if (!_AJAX) {
			include_spip('inc/minipres');
			minipres('Arguments incompris');
		} else {
			spip_log('Arguments incompris dans action dereferencer_traduction_rubrique');
			return false;
		}
	}

	$objet = table_objet($type);
	$table = table_objet_sql($objet);

	// modifier le groupe de traduction de $id_ancienne_reference (SQL le trouvera)
	sql_update($table, array("id_trad" => $id_nouvelle_reference), "id_trad=" . $id_ancienne_reference);
	
}
