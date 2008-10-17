<?php

// ajouter un checkbox 'mots techniques' sur le formulaire CVT editer_groupe_mots
function mots_techniques_editer_contenu_objet($flux){
	
	if ($flux['args']['type']=='groupe_mot') {
		include_spip('public/assembler');
		$flux['args']['contexte']['technique'] = sql_getfetsel('technique','spip_groupes_mots','id_groupe='.sql_quote($flux['args']['contexte']['id_groupe']));
		$mt = recuperer_fond('formulaires/inc-groupe_mots_techniques', $flux['args']['contexte']);
		spip_log($mt,'ploup');
		$flux['data'] = preg_replace('%(<!--extra-->)%is', '$1'."\n".$mt, $flux['data']);
	}
	return $flux;
}

// ajouter le champ technique soumis lors de la soumission du formulaire CVT editer_groupe_mots
function mots_techniques_pre_edition($flux){
	if ($flux['args']['table']=='spip_groupes_mots' AND $flux['args']['action']=='modifier') {
		if (_request('mt_technique_present')=='oui') {
			$technique = _request('technique'); 
			$flux['data']['technique'] = ($technique=='oui')?'oui':'';
		}
	}
	return $flux;
}


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
