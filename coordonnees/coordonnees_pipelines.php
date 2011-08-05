<?php

/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/

/**
 * Informations sur les objets où peut s'appliquer les coordonnees 
 *
 * @param String $quoi info que l'on veut recuperer (sinon tout le tableau)
 * @return Array Liste d'objet et quelques définitions (titre, exec)
**/
function liste_objets_coordonnees($quoi = '') {
	$liste = array(
		'auteur'       => array('titre'=>_T('coordonnees:auteurs'),   'exec'=>'auteur_infos'),
		'article'      => array('titre'=>_T('coordonnees:articles'),  'exec'=>'articles'),
		'rubrique'     => array('titre'=>_T('coordonnees:rubriques'), 'exec'=>'naviguer'),
		'contact'      => array('titre'=>_T('contacts:contacts'),     'exec'=>'contact'),
		'organisation' => array('titre'=>_T('contacts:organisations'),'exec'=>'organisation'),
	);
	
	if (!$quoi) {
		return $liste;
	}

	$listeq = array();
	foreach ($liste as $c=>$v) {
		$listeq[$c] = $v[$quoi];
	}
	return $listeq;	
}



/**
 * Ajout des informations de coordonnées (adresses, mails, numéros)
 * sur la page de visualisation d'un auteur
**/
function coordonnees_affiche_milieu($flux) {
	// cas SPIP 3 ?
	if (function_exists('trouver_objet_exec')) {
		$exec = trouver_objet_exec(_request('exec'));
		// pas en édition
		if ($exec['edition']) {
			return $flux;
		}
		$_id = $exec['id_table_objet'];
		
	} else {
		$exec = $flux['args']['exec'];
	}

	$liste = liste_objets_coordonnees('exec');
	$liste = array_flip($liste);
	
	if (isset($liste[$exec]) and $type = $liste[$exec]) {
		// c'est un exec que l'on peut afficher
		// verifions qu'il est coche dans la conf
		$conf = unserialize($GLOBALS['meta']['coordonnees']);
		if (in_array($type, $conf['objets'])) {
			// on doit l'afficher
			// seulement si on a un identifiant
			$_id = id_table_objet($type);

			if (isset($flux['args'][$_id]) and $id = $flux['args'][$_id]) {
				include_spip('inc/presentation');
				$contexte = array(
					'objet' => $type,
					'id_objet' => $id
				);
				$flux['data'] .= recuperer_fond('prive/boite/coordonnees', $contexte, array('ajax'=>true));		
			}
		}
	}

	return $flux;
/*	
	if (
		(($flux['args']['exec'] == 'auteur_infos') AND $id_auteur = $flux['args']['id_auteur'])
		OR (function_exists('trouver_objet_exec')
				AND $exec = trouver_objet_exec(_request('exec'))
		    AND $exec['type'] == 'auteur'
				AND !$exec['edition']
		    AND $id_auteur = $flux['args']['id_auteur'])
		){
			include_spip('inc/presentation');
			$contexte = array(
				'objet' => 'auteur',
				'id_objet' => $id_auteur
			);
			$flux['data'] .= recuperer_fond('prive/boite/coordonnees', $contexte, array('ajax'=>true));
	}
	return $flux;*/
}


/**
 * Ajout de l'objet 'adresse'
 * à la liste des objets pouvant recevoir des champs extras 
**/
function coordonnees_objets_extensibles($objets){
		return array_merge($objets, array(
			'adresse' => _T('coordonnees:adresses'),
		));
}

?>
