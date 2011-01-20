<?php

function formulaires_cite_inserer_modeles_traiter_dist($champs){
	$code = '<'._request('modele');
	//if (_request('id_modele') && _request('id_modele')!='')
		//$code .= _request('id_modele');
	if (_request('variante') && _request('variante')!='')
		$code .= '|'._request('variante');
	//if (_request('classe') && _request('classe')!='')
		//$code .= '|'._request('classe');
	//if (_request('align') && _request('align')!='')
		//$code .= '|'._request('align');
	// On accole le titre à la variante (car il ne faut surtout pas d'espace après la variante)
	if (_request('title'))
		$code .= '|title='._request('title');
	// Pour les autres champs, on fait un retour à la ligne afin d'avoir une syntaxe plus aérée
	// On supprime les champs inutilisés pour une variante donnée
	// (champs qui peuvent être renseignés si l'utilisateur a modifié la variante après une première saisie du formulaire)
	foreach ($champs as $champ) {
		if($champ != 'modele' && $champ != 'variante' && $champ != 'classe' && $champ != 'id_modele' && $champ != 'align' && $champ != 'title' && _request($champ) && _request($champ)!='') {
			// journal
			if ($champ=='journal' && !in_array(_request('variante'),array('journal')))
				$code .= '';
			// site
			if ($champ=='site' && !in_array(_request('variante'),array('web')))
				$code .= '';
			// booktitle
			elseif ($champ=='booktitle' && !in_array(_request('variante'),array('chapter')))
				$code .= '';
			// editors
			elseif ($champ=='editors' && !in_array(_request('variante'),array('chapter')))
				$code .= '';
			// series
			elseif ($champ=='series' && !in_array(_request('variante'),array('book','chapter')))
				$code .= '';
			// report_type
			elseif ($champ=='report_type' && !in_array(_request('variante'),array('report')))
				$code .= '';
			// thesis_type
			elseif ($champ=='thesis_type' && !in_array(_request('variante'),array('thesis')))
				$code .= '';
			// volume
			elseif ($champ=='volume' && !in_array(_request('variante'),array('journal','book','chapter')))
				$code .= '';
			// issue
			elseif ($champ=='issue' && !in_array(_request('variante'),array('journal')))
				$code .= '';
			// number
			elseif ($champ=='number' && !in_array(_request('variante'),array('report')))
				$code .= '';
			// edition
			elseif ($champ=='edition' && !in_array(_request('variante'),array('book','chapter')))
				$code .= '';
			// publisher
			elseif ($champ=='publisher' && !in_array(_request('variante'),array('book','chapter')))
				$code .= '';
			// institution
			elseif ($champ=='institution' && !in_array(_request('variante'),array('report')))
				$code .= '';
			// university
			elseif ($champ=='university' && !in_array(_request('variante'),array('thesis')))
				$code .= '';
			// place
			elseif ($champ=='place' && !in_array(_request('variante'),array('book','chapter','report','thesis')))
				$code .= '';
			// page
			elseif ($champ=='page' && !in_array(_request('variante'),array('book','chapter','journal','report','thesis')))
				$code .= '';
			// isbn
			elseif ($champ=='isbn' && !in_array(_request('variante'),array('book','chapter')))
				$code .= '';
			// issn
			elseif ($champ=='issn' && !in_array(_request('variante'),array('journal')))
				$code .= '';
			// doi
			elseif ($champ=='doi' && !in_array(_request('variante'),array('book','chapter','journal')))
				$code .= '';
			// year
			elseif ($champ=='year' && !in_array(_request('variante'),array('book','chapter','journal','report','thesis')))
				$code .= '';
			// accessdate
			if ($champ=='accessdate' && !in_array(_request('variante'),array('web')))
				$code .= '';
			// Cas général
			else
				$code .= "\n   |$champ="._request($champ);
		}
	}
	$code .= "\n>";
	return $code;
}

?>