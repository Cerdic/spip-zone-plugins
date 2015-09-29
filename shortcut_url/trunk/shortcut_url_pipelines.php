<?php

/**
 * Pipeline pour shortcut_url
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\shortcut_url\pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function autoriser_menugrandeentree($faire, $type, $id, $qui, $opt){

		if(!in_array($type ,array('menuaccueil','menuedition','menuadministration','menuconfiguration')))
			return false;
		if($type != 'menuaccueil' && $qui['statut'] != '0minirezo')
			return false;

	return true;
}

function autoriser_auteurs_menu($faire, $type, $id, $qui, $opt){
	return true;
}

function autoriser_articles_menu($faire, $type, $id, $qui, $opt){
	return false;
}

function autoriser_rubriques_menu($faire, $type, $id, $qui, $opt){
	return false;
}

function autoriser_documents_menu($faire, $type, $id, $qui, $opt){
	return false;
}

function shortcut_url_affiche_milieu($flux) {

	if (trouver_objet_exec($flux['args']['exec'] == "auteur") && $flux['args']['id_auteur']){
		$texte = recuperer_fond('prive/objets/editer/shortcut_url_auteurs',
			array(
				'id_auteur'=>$id_auteur
			)
		);
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] .= $texte;
		else
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
	}

	return $flux;
}

/**
 * Ajoute les css pour shortcut_url chargées dans le privé
 * 
 * @param string $flux Contenu du head HTML concernant les CSS
 * @return string       Contenu du head HTML concernant les CSS
 */
function shortcut_url_header_prive_css($flux) {

	$css = find_in_path('css/shortcut_url.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='".direction_css($css)."' />\n";

	return $flux;
}
?>
