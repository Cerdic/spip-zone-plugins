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
	// Les dates sont en ISO 8601 UTC, et parfois avec horaire parfois sans, il faut transformer et vérifier
	
	// Si c'est juste la date, on tronque le champ de comparaison
	if ($from) {
		if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $from)) {
			$champ_de_creation_from = "substring($champ_de_creation, 1, 10)";
			$champ_de_modification_from = "substring($champ_de_modification, 1, 10)";
		}
		else {
			$from = date('Y-m-d H:i:s', strtotime($from));
			$champ_de_creation_from = $champ_de_creation;
			$champ_de_modification_from = $champ_de_modification;
		}
	}
	
	// Pareil pour until
	if ($until) {
		if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $until)) {
			$champ_de_creation_until = "substring($champ_de_creation, 1, 10)";
			$champ_de_modification_until = "substring($champ_de_modification, 1, 10)";
		}
		else {
			$until = date('Y-m-d H:i:s', strtotime($until));
			$champ_de_creation_until = $champ_de_creation;
			$champ_de_modification_until = $champ_de_modification;
		}
	}
	
	// S'il y a au moins une des deux dates
	if ($from or $until) {
		$where = array(
			'or',
			'('.($from ? sql_quote($from).' <= '.$champ_de_creation_from : '').(($from and $until) ? ' and ' : '').($until ? $champ_de_creation_until.' <= '.sql_quote($until) : '').')',
			'('.($from ? sql_quote($from).' <= '.$champ_de_modification_from : '').(($from and $until) ? ' and ' : '').($until ? $champ_de_modification_until.' <= '.sql_quote($until) : '').')',
		);
	}
	// Sinon aucun critère
	else {
		$where = '';
	}
	
	return $where;
}
