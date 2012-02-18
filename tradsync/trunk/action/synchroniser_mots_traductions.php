<?php

function action_synchroniser_mots_traductions_dist() {

	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	list($type, $ids) = explode('/',$arg);
	if (!$type = objet_type($type) or !$ids) {
		if (!_AJAX) {
			include_spip('inc/minipres');
			minipres('Arguments incompris');
		} else {
			spip_log('Arguments incompris dans action synchroniser_mots_traductions');
			return false;
		}
	}

	$ids = explode('-',$ids);
	$objet = table_objet($type);

	if (!((count($ids) > 1)
			? autoriser('synchronisermots',$type)
			: autoriser('synchronisermots',$type, $ids[0]))) {
		if (!_AJAX) {
			include_spip('inc/minipres');
			minipres();
		} {
			spip_log('Autorisation rate dans action synchroniser_mots_traductions');
			return false;
		}
	}


	// pour chaque element, on recupere les mots cles de l'original
	// et on les copie dedans s'il n'existent pas...
	//
	// on ne supprime pas des mots cles en plus dans la traduction
	// (a voir ulterieurement pour une option la dessus)...
	$_id_objet = id_table_objet($objet);
	$table = table_objet_sql($objet);

	include_spip('action/editer_liens');
	// les ids sont les traductions
	// dont on veut synchroniser les mots avec leur source d'origine
	foreach ($ids as $id) {
		// on ne prend que les elements qui ne sont des traductions, pas les originaux
		// et on recupere l'id de l'objet source
		if ($id_source = sql_getfetsel('id_trad', $table, array("$_id_objet=".sql_quote($id), 'id_trad>'.sql_quote(0), "$_id_objet <> id_trad"))) {
			// tous les mots sur l'objet d'origine de la traduction
			$mots = objet_trouver_liens(array('mot'=>'*'), array($type=>$id_source));
			foreach ($mots as $m) {
				objet_associer(array('mot'=>$m['mot']), array($type=>$id), $m);
			}
		}
	}
}

?>
