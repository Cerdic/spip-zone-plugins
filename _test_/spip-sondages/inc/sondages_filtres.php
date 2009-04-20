<?php


	function sondages_calculer_pourcentage($id_sondage, $id_choix) {
		$total_sondage	= intval(sql_countsel('spip_avis', 'id_sondage='.intval($id_sondage)));
		$total_avis		= intval(sql_countsel('spip_avis', 'id_choix='.intval($id_choix).' AND id_sondage='.intval($id_sondage)));
		if ($total_sondage == 0) {
			return 0;
		} else {
			$pourcentage = ( ($total_avis / $total_sondage) * 100 );
			$pourcentage = number_format($pourcentage, 1, '.', '');
			return $pourcentage;
		}
	}


	function sondages_pourcentage($total_avis, $id_sondage) {
		$total_sondage	= intval(sql_countsel('spip_avis', 'id_sondage='.intval($id_sondage)));
		$pourcentage = ( ($total_avis / $total_sondage) * 100 );
		$pourcentage = number_format($pourcentage, 1, '.', '');
		return $pourcentage;
	}


	function sondages_largeur($total_avis, $id_sondage, $largeur_max) {
		$total_sondage = intval(sql_countsel('spip_avis', 'id_sondage='.intval($id_sondage)));
		$max = sql_getfetsel('COUNT(id_choix) AS total', 'spip_avis', 'id_sondage='.intval($id_sondage), 'id_choix', 'total DESC', '1');
		if ($max == 0)
			return '';
		$rapport = $total_avis / $max;
		$largeur = $rapport * $largeur_max;
		return $largeur;
	}


?>