<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_cite_inserer_modeles_traiter_dist($champs){
	$code = '<'._request('modele');
	if (_request('id_modele') && _request('id_modele')!='')
		$code .= _request('id_modele');
	if (_request('align') && _request('align')!='')
		$code .= '|'._request('align');
	if (_request('variante') && _request('variante')!='')
		$code .= '|'._request('variante');
	//if (_request('classe') && _request('classe')!='')
		//$code .= '|'._request('classe');
	// On accole le titre � la variante (car il ne faut surtout pas d'espace apr�s la variante)
	if (_request('title'))
		$code .= '|title='._request('title');
	// Pour les autres champs, on fait un retour � la ligne afin d'avoir une syntaxe plus a�r�e
	foreach ($champs as $champ) {
		if($champ != 'modele' && $champ != 'variante' && $champ != 'classe' && $champ != 'id_modele' && $champ != 'align' && $champ != 'title' && _request($champ) && _request($champ)) {
			$code .= "\n   |$champ="._request($champ);
		}
	}
	$code .= "\n>";
	return $code;
}

?>