<?php

//
// Recapitulons nos autorisations
//
// - pour les mots d'un groupe
// * voirmots
// * creermots
// * modifiermots
// * liermots
//
// - pour un groupe
// * voir
// * creer
// * modifier
//
function mots_techniques_autoriser_groupemots($flux){
	$args = &$flux['args'];
	if ($args['technique'] == 'oui'){
		switch ($args['faire']){
			
			// voir le groupe
			case 'voir':
				// $flux['autoriser'] = true;
				break;
				
			// voir les mots du groupe
			case 'voirmots':
				// $flux['autoriser'] = ($args['qui']['statut'] == '0minirezo' AND !$args['qui']['restreint']);
				break;
			
			// creer un groupe
			case 'creer':
			// modifier un groupe
			case 'modifier':
				// $flux['autoriser'] =	$args['qui']['statut'] == '0minirezo' AND !$args['qui']['restreint'];
				break;
				
			// creer des mots dans ce groupe
			case 'creermots':
				// $flux['autoriser'] = false;
				break;
			// modifier des mots dans ce groupe (y compris suppression)
			case 'modifiermots':			
			// lier des mots de ce groupe a des objets (articles, rubriques, etc.)
			case 'liermots':
				// $flux['autoriser'] &= ($args['qui']['statut'] == '0minirezo' AND !$args['qui']['restreint']);
				// $flux['autoriser'] = ($args['row'][substr($args['qui']['statut'],1)]=='oui');
				break;
		}
	}

	return $flux;
}
?>
