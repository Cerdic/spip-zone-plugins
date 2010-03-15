<?php

function formulaires_recherche_par_mots_charger_dist($filtre_groupes = NULL){
	return 
		array(
			'filtre_groupes' => $filtre_groupes,
			'id_groupe'=>_request('id_groupe'),
			'mots'=>_request('mots')
		);
}

function critere_mots_enleve_mot_de_liste($listemots, $id_mot) {
	unset($listemots[array_search($id_mot,$listemots)]);
	return $listemots;
}

?>