<?php

function formulaires_media_inserer_modeles_traiter_dist($champs){
	$code = '<'._request('modele');
	if (_request('id_modele') && _request('id_modele')!='')
		$code .= _request('id_modele');
	if (_request('variante') && _request('variante')!='')
		$code .= '|'._request('variante');
	if (_request('classe') && _request('classe')!='')
		$code .= '|'._request('classe');
	if (_request('align') && _request('align')!='')
		$code .= '|'._request('align');
	foreach ($champs as $champ) {
		if($champ != 'modele' && $champ != 'variante' && $champ != 'classe' && $champ != 'id_modele' && $champ != 'align' && _request($champ) && _request($champ)!='') {
			// Cas de la légende
			if($champ == 'legende' && _request($champ)=='personnalisee')
				$code .= '';
			elseif (_request('legende')!='personnalisee' && in_array($champ,array('titre','descriptif','credits','type','poids')))
				$code .= '';
			// Cas de la taille
			if($champ == 'taille' && _request($champ)=='personnalisee')
				$code .= '';
			elseif (_request('taille')!='personnalisee' && in_array($champ,array('hauteur','largeur')))
				$code .= '';
			// Cas général
			elseif($champ == _request($champ))
				$code .= "|$champ";
			else
				$code .= "|$champ="._request($champ);
		}
	}
	$code .= '>';
	return $code;
}

?>