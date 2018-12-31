<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function menus_header_prive($flux) {
	$css = find_in_path('css/menuspip.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}

function menus_pre_boucle($boucle) {
	if ($boucle->type_requete == 'menus') {
		$id_table = $boucle->id_table;
		$id_menus_entree = "$id_table.id_menus_entree";
		if (!isset($boucle->modificateur['criteres']['id_menus_entree'])
			and !isset($boucle->modificateur['criteres']['id_menu'])
			and !isset($boucle->modificateur['criteres']['identifiant'])) {
			$boucle->where[] = array(sql_quote('='), sql_quote($id_menus_entree), 0);
		}
	}
	return $boucle;
}

function menus_menus_lister_disponibles($flux) {
	return $flux;
}

function menus_declarer_url_objets($array) {
	$array[] = 'menu';
	return $array;
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function menus_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);
	include_spip('inc/config');

	// Menus sur les objets configurés
	if ($e !== false
		and isset($e['edition'], $e['table_objet_sql'])
		and !$e['edition']
		and in_array($e['table_objet_sql'], lire_config('menus/objets', array()))
	) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'menus',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

/**
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function menus_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('menu'=>'*'), '*');

	return $flux;
}