<?php

function formulaires_recherche_par_mots_charger_dist($filtre_groupes = NULL){
	return 
		array(
			'id_groupe' => $filtre_groupes,
			'le_groupe'=>_request('le_groupe'),
			'mots'=>_request('mots')
		);
}

function critere_mots_enleve_mot_de_liste($listemots, $id_mot) {
	unset($listemots[array_search($id_mot,$listemots)]);
	return $listemots;
}

?>