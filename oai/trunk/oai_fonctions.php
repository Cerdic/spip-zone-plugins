<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function critere_oaifromuntil_dist($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	
	if (count($crit->param) == 4) {
		$champ_de_creation = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
		$champ_de_modification = calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent);
		$from = calculer_liste($crit->param[2], array(), $boucles, $boucles[$idb]->id_parent);
		$until = calculer_liste($crit->param[3], array(), $boucles, $boucles[$idb]->id_parent);
	
		$boucle->where[] = "calculer_critere_oaifromuntil($champ_de_creation, $champ_de_modification, $from, $until)";
	}
}

// Calcul du critère {oai_from_until}
function calculer_critere_oaifromuntil($champ_de_creation, $champ_de_modification, $from, $until){
	// S'il y a au moins une des deux dates
	if ($from or $until) {
		$where = array(
			'or',
			'('.($from ? sql_quote($from).' <= '.$champ_de_creation : '').(($from and $until) ? ' and ' : '').($until ? $champ_de_creation.' <= '.sql_quote($until) : '').')',
			'('.($from ? sql_quote($from).' <= '.$champ_de_modification : '').(($from and $until) ? ' and ' : '').($until ? $champ_de_modification.' <= '.sql_quote($until) : '').')',
		);
	}
	// Sinon aucun critère
	else {
		$where = '';
	}
	
	return $where;
}
