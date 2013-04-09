<?php

function projets_organisations_afficher_complement_objet($flux) {

	$texte = "";
	$type = $flux['args']['type'];

	// projets sur les organisations
	if (!$e['edition'] AND $type == 'organisation') {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projets',
			'objet' => $type,
			'id_objet' => $flux['args']['id']
		));
	}

	// organisations sur les projets
	if (!$e['edition'] AND $type == 'projet') {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projet',
			'objet' => $flux['args']['id'],
			'id_objet' => 'organisation'
		));
	}

	if ($texte) {
		$flux['data'] .= $texte;
	}

	return $flux;
}



