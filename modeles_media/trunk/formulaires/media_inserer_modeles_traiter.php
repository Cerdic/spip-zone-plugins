<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

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
			// Cas de la l�gende
			if($champ == 'legende' && _request($champ)=='personnalisee')
				$code .= '';
			elseif($champ == 'legende2' && _request($champ)=='personnalisee')
				$code .= '';
			elseif ($champ == 'legende2' && _request($champ))
				$code .= "|legende="._request($champ);
			elseif (_request('legende')!='personnalisee' && _request('legende2')!='personnalisee' && in_array($champ,array('titre','descriptif','credits','type','poids')))
				$code .= '';
			// Cas de la taille
			elseif($champ == 'taille' && _request($champ)=='personnalisee')
				$code .= '';
			elseif (_request('taille')!='personnalisee' && in_array($champ,array('hauteur','largeur')))
				$code .= '';
			// Cas de titre_lien
			elseif ($champ == 'titre_lien' && _request('lien')=='')
				$code .= '';
			// Cas g�n�ral
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