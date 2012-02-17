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
	$in = sql_in($_id_objet, $ids);

	// on ne prend que les elements qui ne sont des traductions, pas les originaux
	if ($res = sql_select(array($_id_objet, 'id_trad'),	$table, array($in, 'id_trad>'.sql_quote(0), "$_id_objet <> id_trad"))) {
		while ($trad = sql_fetch($res)) {
			$id_ori = $trad['id_trad'];
			$id_trad = $trad[$_id_objet];
			$m_ori = $m_trad = array();
			if ($mots = sql_select('id_mot', 'spip_mots_'.$objet, "$_id_objet = ".sql_quote($id_trad))) {
				while ($mot = sql_fetch($mots)) {
					$m_trad[] = $mot['id_mot'];
				}
			}
			if ($mots = sql_select('id_mot', 'spip_mots_'.$objet, "$_id_objet = ".sql_quote($id_ori))) {
				while ($mot = sql_fetch($mots)) {
					$m_ori[] = $mot['id_mot'];
				}
			}			
			$absents = array_diff($m_ori, $m_trad);
			$surplus = array_diff($m_trad, $m_ori);
			$vals = array();
			if ($absents) {
				foreach ($absents as $absent) {
					$vals[] = array($_id_objet => $id_trad, 'id_mot' => $absent);
				}
				sql_insertq_multi('spip_mots_'.$objet, $vals);
			}
			/*
			if ($surplus) {
				foreach ($surplus as $trop) {
					sql_delete('spip_mots_'.$objet, array($_id_objet => $id_trad, 'id_mot' => $trop));
				}
			}
			*/		
		}
	}

}

?>
