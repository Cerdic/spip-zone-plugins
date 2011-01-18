<?php
/**
 * Plugin Catalogue pour Spip 2.0
 * Licence GPL (c) 2009-2010 - Ateliers CYM
 */
function catalogue_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('catalogue.css').'" media="all" />';
	return $flux;
}

function catalogue_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	
	// si on est sur la page ?exec=articles
	if ($exec=='articles' AND $id_article = $flux['args']['id_article']) {
	
		$id_article = $flux['args']['id_article'];
		if (!$id_rubrique = $flux['args']['id_rubrique']) {
			$id_rubrique = sql_getfetsel('id_rubrique', 'spip_articles', 'id_article='. $id_article);
		}

		if (afficher_catalogue_dans_rubrique($id_rubrique)) {
			// a corriger $_GET... trop permissif
			$contexte = $_GET;
			$flux['data'] .= recuperer_fond('prive/boite/catalogue_article', $contexte, array('ajax'=>true));
		}
	}

	return $flux;
}


/**
 * Détection des rubriques ayant un catalogue 
 *
 * @param int $id_rubrique : rubrique demandee (cette rubrique a t-elle un catalogue ?)
 * @return bool
**/
function afficher_catalogue_dans_rubrique($id_rubrique) {
	static $rubs = array();
	static $rubs_catalogue = false;

	// deja trouve
	if (isset($rubs[$id_rubrique])) {
		return $rubs[$id_rubrique];
	}

	// init
	if ($rubs_catalogue === false) {
		$r = lire_config('catalogue/branches');
		include_spip('spip_bonux_fonctions');
		$rubs_catalogue = picker_selected($r, 'rubrique'); // fn de spip bonux
	}

	// trouve
	if (in_array($id_rubrique, $rubs_catalogue)) {
		$rubs[$id_rubrique] = true;
		return true;
	}

	// rechercher dans la parente
	if ($id_parent = sql_getfetsel('id_parent', 'spip_rubriques', 'id_rubrique='.$id_rubrique)) {
		 $rubs[$id_rubrique] = afficher_catalogue_dans_rubrique($id_parent);
		 return $rubs[$id_rubrique];
	}

	// perdu :)
	return false;
}
?>
