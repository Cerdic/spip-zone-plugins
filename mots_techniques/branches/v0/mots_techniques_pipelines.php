<?php

/**
 * Prendre en compte les criteres "technique" et "tout"
 * sur les boucles mots et groupes_mots
 * (on restreint par defaut aux mots cles non techniques)
 */
function mots_techniques_pre_boucle($boucle){
	// MOTS
	if ($boucle->type_requete == 'mots') {
		$id_table = $boucle->id_table;
		// Restreindre aux mots cles non techniques
		// les modificateurs ne se creent que sur les champs de la table principale
		// pas sur une jointure, il faut donc analyser les criteres passes pour
		// savoir si l'un deux est un 'technique'...
		// pff...
		$technique = false;
		foreach($boucle->criteres as $c){
			if ($c->param[0][0]->texte == 'technique') {
				$technique = true;
				break;
			}
		}
		if (!$technique && 
			!isset($boucle->modificateur['tout'])) {
				$boucle->from["groupes"] =  "spip_groupes_mots";
				$boucle->where[]= array("'='", "'groupes.id_groupe'", "'$id_table.id_groupe'");
				$boucle->where[]= array("'='", "'groupes.technique'", "'\"\"'");	
		} 
	// GROUPES_MOTS		
	} elseif ($boucle->type_requete == 'groupes_mots') {
		$id_table = $boucle->id_table;
		$mtechnique = $id_table .'.technique';
		// Restreindre aux mots cles non techniques
		if (!isset($boucle->modificateur['criteres']['technique']) && 
			!isset($boucle->modificateur['tout'])) {
				$boucle->where[]= array("'='", "'$mtechnique'", "'\"\"'");
		}		
	}
	return $boucle;
}


?>
