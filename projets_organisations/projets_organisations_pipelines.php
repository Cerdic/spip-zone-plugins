<?php

function projets_organisations_affiche_milieu($flux) {

	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

// projets sur les organisations
	if (!$e['edition'] AND $e['type'] == 'organisation') {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projets',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

// organisations sur les projets
	if (!$e['edition'] AND $e['type'] == 'projet') {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projet',
			'objet' => $flux['args'][$e['id_table_objet']],
			'id_objet' => 'organisation'
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}




?>