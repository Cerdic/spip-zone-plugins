<?php

function maj_ajouter_onglets($onglets){
	if($onglets['args'] == 'administration') {
		$onglets['data']['mise_a_jour'] =
			new Bouton(find_in_path('images/maj.png'), _T('maj:mise_a_jour'),
				generer_url_ecrire("mise_a_jour",""));
	}
	return $onglets;
}

?>