<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/boutons');


/**
 * definir la liste des boutons du haut et de ses sous-menus
 * On defini les boutons a metrtre selon les droits de l'utilisateur
 * puis on balance le tout au pipeline "ajouter_boutons" pour que des plugins
 * puissent y mettre leur grain de sel
 */
// http://doc.spip.org/@definir_barre_boutons
function definir_barre_boutons() {
	$boutons_admin=array();

	// ajouter les boutons issus des plugin via plugin.xml
	// avant l'icone de configuration
	if (function_exists('boutons_plugins')
	  AND is_array($liste_boutons_plugins = boutons_plugins())){
		foreach($liste_boutons_plugins as $id => $infos){
			// les boutons principaux ne sont pas soumis a autorisation
			if (!($parent = $infos['parent']) OR autoriser('bouton',$id)){
				if ($parent AND isset($boutons_admin[$parent]))
					$boutons_admin[$parent]->sousmenu[$id]= new Bouton(
					  find_in_path($infos['icone']),  // icone
					  $infos['titre'],	// titre
					  $infos['url']?$infos['url']:null,
					  $infos['args']?$infos['args']:null
					  );
				if (!$parent
				// provisoire, eviter les vieux boutons
				AND (!in_array($id,array('forum','statistiques_visites')))
				
				) {
					$position = $infos['position']?$infos['position']:count($boutons_admin);
					$boutons_admin = array_slice($boutons_admin,0,$position)
					+array($id=> new Bouton(
					  find_in_path($infos['icone']),  // icone
					  $infos['titre'],	// titre
					  $infos['url']?$infos['url']:null,
					  $infos['args']?$infos['args']:null
					  ))
					+ array_slice($boutons_admin,$position,100);
				}
			}
		}
	}

	return pipeline('ajouter_boutons', $boutons_admin);
}

/**
 * Creer l'url a partir de exec et args, sauf si c'est deja une url formatee
 *
 * @param string $url
 * @param string $args
 * @return string
 */
// http://doc.spip.org/@bandeau_creer_url
function bandeau_creer_url($url, $args=""){
	if (preg_match(',[\/\?],',$url))
		return $url;
	else
		return generer_url_ecrire($url,$args);
}

/**
 * Lister le contenu d'un sous menu dans des elements li de class $class
 *
 * @param array $sousmenu
 * @param string $class
 * @return string
 */
function bando_lister_sous_menu($sousmenu,$class=""){
	$class = $class ? " class='$class'":"";
	$sous = "";
	if (is_array($sousmenu)){
		$sous = "";		 
		foreach($sousmenu as $souspage => $sousdetail){
			$url = bandeau_creer_url($sousdetail->url?$sousdetail->url:$souspage, $sousdetail->urlArg);
			$sous .= "<li$class>"
		 . "<a href='$url' id='bando2_$souspage'>"
		 . _T($sousdetail->libelle)
		 . "</a>"
		 . "</li>";
		}
	}
	return $sous;
}

/**
 * Construire le bandeau de navigation principale de l'espace prive
 * a partir de la liste des boutons definies dans un tableau d'objets
 *
 * @param array $boutons
 * @return string
 */
function bando_navigation($boutons)
{
	$res = "";
	
	$first = " class = 'first'";
	foreach($boutons as $page => $detail){
		
		// les icones de premier niveau sont ignoree si leur sous menu est vide
		// et si elles pointent vers exec=navigation
		if (
		 (is_array($detail->sousmenu) AND count($detail->sousmenu))
		 OR ($detail->url!='navigation')) {
			$url = bandeau_creer_url($detail->url?$detail->url:$page, $detail->urlArg);
			$res .= "<li$first>"
			 . "<a href='$url' id='bando1_$page'>"
			 . _T($detail->libelle)
			 . "</a>";
		}
		
		$sous = bando_lister_sous_menu($detail->sousmenu);
		$res .= $sous ? "<ul>$sous</ul>":"";
		 
		$res .= "</li>";
		$first = "";
	}

	return "<div id='bando_navigation'><div class='largeur'><ul>\n$res</ul><div class='nettoyeur'></div></div></div>";
}

/**
 * Construire le bandeau identite de l'espace prive
 *
 * @return unknown
 */
function bando_identite(){

	$nom_site = typo($GLOBALS['meta']['nom_site']);
	$img_info = find_in_path('images/information.png');
	$url_config_identite = generer_url_ecrire('config_identite');
	
	$res = "<p class='nom_site_spip'>"
	  . "<strong class='nom'> $nom_site </strong>"
	  . " |"
	  . "<a class='info' title='Informations sur $nom_site' href='$url_config_identite'><img alt='Informations sur $nom_site' src='$img_info' /></a>"
	  . "| "
	  . "<a class='voir' href='"._DIR_RACINE."'>"._T('bando:icone_visiter_site')."</a>"
	  . "</p>";
	 
	
	$moi = typo($GLOBALS['visiteur_session']['nom']);
	$img_langue = find_in_path('images/langues.png');
	$url_aide = generer_url_ecrire('aide_index',"var_lang=".$GLOBALS['spip_lang']);
	$url_lang = generer_url_ecrire('config_langage');

	$res .= "<p class='session'>"
	  . "<strong class='nom'>$moi</strong>"
	  . " |"
	  . "<a title='Mes informations personnelles' href='".generer_url_ecrire("auteur_infos","id_auteur=".$GLOBALS['visiteur_session']['id_auteur'])."'><img alt='"._T('icone_informations_personnelles')."' src='$img_info'/></a>"
	  . "| "
	  . "<a class='menu_lang' href='$url_lang' title='"._T('bando:titre_config_langage')."'><img alt='"._T('bando:titre_config_langage')."' src='$img_langue'/>".traduire_nom_langue($GLOBALS['spip_lang'])."</a>"
	  . " | "
	  . "<a class='aide' onclick=\"window.open('$url_aide', 'spip_aide', 'scrollbars=yes,resizable=yes,width=740,height=580');return false;\" href='$url_aide'>"._T('icone_aide_ligne')."</a>"
	  . " | "
	  // $auth_can_disconnect?
	  . "<a href='".generer_url_action("logout","logout=prive")."'>"._T('icone_deconnecter')."</a>"
	  . "</p>";
	
	return "<div id='bando_identite'><div class='largeur'>\n$res<div class='nettoyeur'></div></div></div>";

}


/**
 * Construire tout le bandeau superieur de l'espace prive
 *
 * @param unknown_type $rubrique
 * @param unknown_type $sous_rubrique
 * @param unknown_type $largeur
 * @return unknown
 */
function inc_bandeau_dist($rubrique, $sous_rubrique, $largeur)
{
	return "<div class='avec_icones' id='bando_haut'>"
		. bando_identite()
		. bando_navigation(definir_barre_boutons())
		. "</div>"
		;
	
}
?>
