<?php

// surcharger les boucles FORUMS
// pour afficher uniquement les forums public meme en preview
function comments_pre_boucle($boucle){
	if ($boucle->type_requete == 'forums') {
		$id_table = $boucle->id_table;
		$mstatut = $id_table .'.statut';
		// Par defaut, selectionner uniquement les forums sans mere
		// Les criteres {tout} et {plat} inversent ce choix
		if (!isset($boucle->modificateur['tout']) AND !isset($boucle->modificateur['plat'])) {
			array_unshift($boucle->where,array("'='", "'$id_table." ."id_parent'", 0));
		}
		// Restreindre aux elements publies
		if (!$boucle->modificateur['criteres']['statut']) {
				array_unshift($boucle->where,array("'='", "'$mstatut'", "'\\'publie\\''"));
		}
	}
	return $boucle;
}

?>