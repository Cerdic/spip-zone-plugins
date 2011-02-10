<?php

include_spip('inc/rang');

function rang_formulaire_charger($flux) {
	switch ($flux['args']['form']) {
		case 'editer_rubrique' :
		case 'editer_article' :
			if ($flux['data']['rang']) {
				$flux['data']['titre'] = $flux['data']['rang'].'. '.$flux['data']['titre'];         
			}
			break;
	}
	return $flux;
}

function rang_formulaire_verifier($flux) {
	switch ($flux['args']['form'] ) {
		case 'editer_rubrique' :
		case 'editer_article' :
			$array = extraire_rang(_request('titre'));
			set_request('titre',$array['titre']);
			set_request('rang',$array['rang']);
			break;
	}
	return $flux;
}

//ajouter la valeur de rang dans la base
function rang_formulaire_traiter($flux) {
	switch($flux['args']['form']) {
		case 'editer_rubrique' :
			update_rang(_request('rang'),'rubrique',$flux['data']['id_rubrique']);
			break;
		case 'editer_article' :
			update_rang(_request('rang'),'article',$flux['data']['id_article']);
			break;
	}
	return $flux;
}


?>
