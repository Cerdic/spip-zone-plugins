<?php
	
	//modif des lignes 353 à 371
		if ($lettre->statut == 'brouillon') {
			$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($lettre->id_rubrique);
			$rubriques_virgules = implode(',', $rubriques);
			$abonnes = array();
			$res = sql_select('id_abonne', 'spip_abonnes_rubriques', 'id_rubrique IN ('.$rubriques_virgules.')');
			while ($arr = sql_fetch($res))
				$abonnes[] = $arr['id_abonne'];
			$abonnes_virgules = implode(',', $abonnes);
			if (count($abonnes))
				echo afficher_objets('abonne', _T('lettresprive:tous_abonnes_rubrique'), array('FROM' => 'spip_abonnes', 'WHERE' => 'id_abonne IN ('.$abonnes_virgules.')', 'ORDER BY' => 'maj DESC'), array('id_rubrique' => $lettre->id_rubrique));
		} else {
			$abonnes = array();
			$res = sql_select('id_abonne', 'spip_abonnes_lettres', 'id_lettre='.$lettre->id_lettre);
			while ($arr = sql_fetch($res))
				$abonnes[] = $arr['id_abonne'];
			$abonnes_virgules = implode(',', $abonnes);
			if (count($abonnes))
				echo afficher_objets('abonne', _T('lettresprive:les_abonnes_suivants_ont_recu_cette_lettre'), array('FROM' => 'spip_abonnes', 'WHERE' => 'id_abonne IN ('.$abonnes_virgules.')', 'ORDER BY' => 'maj DESC'), array('id_lettre' => $lettre->id_lettre));
		}
?>