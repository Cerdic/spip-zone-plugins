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
/*
	$boutons_admin['naviguer'] = new Bouton(
		"documents-48$spip_lang_rtl.png", 'icone_edition_site');

	$boutons_admin['auteurs'] = new Bouton(
		'redacteurs-48.png', 'icone_auteurs');

	// autoriser('configurer') => forcement admin complet (ou webmestre)
	if (autoriser('configurer')) {
		$boutons_admin['configuration']=
		  new Bouton('administration-48.png', 'icone_configuration_site');
	}
	// autres admins (restreints ou non webmestres) peuvent aller sur les backups
	else
	if (autoriser('sauvegarder', 'admin_tech')) {
		$boutons_admin['admin_tech']=
		  new Bouton('administration-48.png', 'texte_sauvegarde_base');
	}

	$boutons_admin['espacement']=null;

	$urlAide= generer_url_ecrire('aide_index')."&amp;var_lang=$spip_lang";
	$boutons_admin['aide_index']=
		  new Bouton('aide-48'.$spip_lang_rtl.'.png', 'icone_aide_ligne',
					 $urlAide, null, "javascript:window.open('$urlAide', 'spip_aide', 'scrollbars=yes,resizable=yes,width=740,height=580');", 'aide_spip');

	$boutons_admin['visiter']=
		new Bouton("visiter-48$spip_lang_rtl.png", 'icone_visiter_site',
		url_de_base());

	// les sous menu des boutons, que si on est admin
	if ($GLOBALS['connect_statut'] == '0minirezo'
	AND $GLOBALS['connect_toutes_rubriques']) {

	// sous menu edition

	$sousmenu=array();

	$nombre_articles = sql_fetsel('id_article', 'spip_auteurs_articles', "id_auteur=".$GLOBALS['connect_id_auteur']);

	if ($nombre_articles > 0) {
		$sousmenu['articles_page']=
		  new Bouton('article-24.gif', 'icone_tous_articles');
	}

	if ($GLOBALS['meta']["activer_breves"] != "non") {
		$sousmenu['breves']=
		  new Bouton('breve-24.gif', 'icone_breves');
	}

	$articles_mots = $GLOBALS['meta']['articles_mots'];
	if ($articles_mots != "non") {
			$sousmenu['mots_tous']=
			  new Bouton('mot-cle-24.gif', 'icone_mots_cles');
	}

	$activer_sites = $GLOBALS['meta']['activer_sites'];
	if ($activer_sites<>'non')
			$sousmenu['sites_tous']=
			  new Bouton('site-24.gif', 'icone_sites_references');

	$n = sql_countsel('spip_documents_liens', 'id_objet>0 AND objet=\'rubrique\'');
	if ($n) {
			$sousmenu['documents_liste']=
			  new Bouton('doc-24.gif', 'icone_doc_rubrique');
	}
	$boutons_admin['naviguer']->sousmenu= $sousmenu;

	// sous menu forum
	$sousmenu=array();

	if (sql_countsel('spip_signatures'))
		$sousmenu['controle_petition']=
			new Bouton("suivi-petition-24.gif", "icone_suivi_pettions");

	// Si le forum a ete desactive, mais qu'il y a un sous-menu de suivi
	// des forums ou des petitions, on colle ce suivi sous le menu "a suivre"
	if ($sousmenu) {
		if (isset($boutons_admin['forum']))
			$boutons_admin['forum']->sousmenu= $sousmenu;
		else
			$boutons_admin['accueil']->sousmenu= $sousmenu;
	}



	// sous menu auteurs

	$sousmenu=array();

	if (avoir_visiteurs(true))
		$sousmenu['auteurs'] =
			new Bouton("fiche-perso.png", 'icone_afficher_visiteurs', null, "statut=!1comite,0minirezo,nouveau");

	$sousmenu['auteur_infos']=
		new Bouton("auteur-24.gif", "icone_creer_nouvel_auteur", null, 'new=oui');

	$boutons_admin['auteurs']->sousmenu= $sousmenu;


	// sous menu configuration
	$sousmenu = array();
	if (autoriser('configurer', 'lang')) {
		$sousmenu['config_lang'] =
			new Bouton("langues-24.gif", "icone_gestion_langues");
		//$sousmenu['espacement'] = null; // les espacements debloquent si on a des icones sur 2 lignes
	}

	if (autoriser('sauvegarder')) {
		$sousmenu['admin_tech']=
			new Bouton("base-24.gif", "icone_maintenance_site");
	}
	if (autoriser('configurer', 'admin_vider')) {
		$sousmenu['admin_vider']=
			new Bouton("cache-24.gif", "onglet_vider_cache");
	}

	// Si _DIR_PLUGINS est definie a '', pas de bouton
	if (_DIR_PLUGINS
	AND autoriser('configurer', 'admin_plugin')) {
		$sousmenu['admin_plugin']=
			new Bouton("plugin-24.gif", "icone_admin_plugin");
	}

	if ($sousmenu)
		$boutons_admin['configuration']->sousmenu= $sousmenu;

	} // fin si admin
	*/

	// ajouter les boutons issus des plugin via plugin.xml
	// avant l'icone de configuration
	if (function_exists('boutons_plugins')
	  AND is_array($liste_boutons_plugins = boutons_plugins())){
		foreach($liste_boutons_plugins as $id => $infos){
			if (autoriser('bouton',$id)){
				if (($parent = $infos['parent']) && isset($boutons_admin[$parent]))
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
					$position = $infos['position']?$infos['position']:-4;
					$boutons_admin += 
					array($id=> new Bouton(
					  find_in_path($infos['icone']),  // icone
					  $infos['titre'],	// titre
					  $infos['url']?generer_url_ecrire($infos['url'],$infos['args']?$infos['args']:''):null
					  ));

				}
			}
		}
	}

	return pipeline('ajouter_boutons', $boutons_admin);
}

// http://doc.spip.org/@bandeau_creer_url
function bandeau_creer_url($url, $args=""){
	if (preg_match(',[\/\?],',$url))
		return $url;
	else
		return generer_url_ecrire($url,$args);
}

function bando_navigation($boutons)
{
	$res = "";
	
	$first = " class = 'first'";
	foreach($boutons as $page => $detail){
		$url = bandeau_creer_url($detail->url?$detail->url:$page, $detail->urlArg);
		$res .= "<li$first>"
		 . "<a href='$url' id='bando1_$page'>"
		 . _T($detail->libelle)
		 . "</a>";
		 
		if (is_array($detail->sousmenu)){
			$sous = "";		 
			foreach($detail->sousmenu as $souspage => $sousdetail){
				$url = bandeau_creer_url($sousdetail->url?$sousdetail->url:$souspage, $sousdetail->urlArg);
				$sous .= "<li>"
			 . "<a href='$url' id='bando2_$souspage'>"
			 . _T($sousdetail->libelle)
			 . "</a>"
			 . "</li>";
			}
			$res .= $sous ? "<ul>$sous</ul>":"";
		}
		$res .= "</li>";
		$first = "";
	}

	return "<div id='bando_navigation'><div class='largeur'><ul>\n$res</ul><div class='nettoyeur'></div></div></div>";
}

function bando_identite(){

	$nom_site = typo($GLOBALS['meta']['nom_site']);
	$img_info = find_in_path('images/information.png');
	
	$res = "<p class='nom_site_spip'>"
	  . "<strong class='nom'> $nom_site </strong>"
	  . " |"
	  . "<a class='info' title='Informations sur $nom_site' href='#'><img alt='Informations sur $nom_site' src='$img_info' /></a>"
	  . "| "
	  . "<a class='voir' href='"._DIR_RACINE."'>Voir le site public</a>"
	  . "</p>";
	 
	
	$moi = typo($GLOBALS['visiteur_session']['nom']);
	$img_langue = find_in_path('images/langues.png');  
	$res .= "<p class='session'>"
	  . "<strong class='nom'>$moi</strong>"
	  . " |"
	  . "<a title='Mes informations personnelles' href='".generer_url_ecrire("auteur_infos","id_auteur=".$GLOBALS['visiteur_session']['id_auteur'])."'><img alt='"._T('icone_informations_personnelles')."' src='$img_info'/></a>"
	  . "| "
	  . "<a class='menu_lang' href='#' title='"._T('info_langues')."'><img alt='"._T('info_langues')."' src='$img_langue'/>".traduire_nom_langue($GLOBALS['spip_lang'])."</a>"
	  . " | "
	  // $auth_can_disconnect?
	  . "<a href='".generer_url_action("logout","logout=prive")."'>"._T('icone_deconnecter')."</a>"
	  . "</p>";
	
	return "<div id='bando_identite'><div class='largeur'>\n$res<div class='nettoyeur'></div></div></div>";

}
/*
function icone_bandeau_principal($detail, $lien, $rubrique_icone = "vide", $rubrique = "", $lien_noscript = "", $sous_rubrique_icone = "", $sous_rubrique = "",$largeur,$decal){
	global $spip_display, $menu_accesskey, $compteur_survol;

	$alt = $accesskey = $title = '';
	$texte = _T($detail->libelle);
	if ($spip_display == 3){
		$title = " title=\"$texte\"";
	}

	if (!$menu_accesskey = intval($menu_accesskey)) $menu_accesskey = 1;
	if ($menu_accesskey < 10) {
		$accesskey = " accesskey='$menu_accesskey'";
		$menu_accesskey++;
	}
	else if ($menu_accesskey == 10) {
		$accesskey = " accesskey='0'";
		$menu_accesskey++;
	}

	$class_select = " style='width:"._LARGEUR_ICONES_BANDEAU."px' class='menu-item boutons_admin".($sous_rubrique_icone == $sous_rubrique ? " selection" : "")."'";

	if (strncasecmp("javascript:",$lien,11)==0) {
		$a_href = "\nonclick=\"$lien; return false;\" href='$lien_noscript' ";
	}
	else {
		$a_href = "\nhref=\"$lien\"";
	}

	$compteur_survol ++;

	if ($spip_display != 1 AND $spip_display != 4) {
		$class ='cellule48';
		$texte = "<span class='icon_fond'><span".http_style_background($detail->icone)."></span></span>".($spip_display == 3 ? '' :  "<span>$texte</span>");
	} else {
		$class = 'cellule-texte';
	}

	return "<li style='width: "
	. _LARGEUR_ICONES_BANDEAU
	. "px' class='$class boutons_admin'><a$accesskey$a_href$class_select$title onfocus=\"$(this).parent().siblings('li').find('.bandeau_sec').hide();\" onkeypress=\"$(this).siblings('.bandeau_sec').show();\">"
	. $texte
	. "</a>\n"
	. bandeau_principal2($detail->sousmenu,$rubrique, $sous_rubrique, $largeur, $decal)
  . "</li>\n";
}*/

/*
function bandeau_principal2($sousmenu,$rubrique, $sous_rubrique, $largeur, $decal) {

	$res = '';
	$coeff_decalage = 0;
	if ($GLOBALS['browser_name']=="MSIE") $coeff_decalage = 1.0;
	$largeur_maxi_menu = $largeur-100;
	$largitem_moy = 85;

	//    if (($rubrique == $page) AND (!_SPIP_AJAX)) {  $page ??????
	if ((!_SPIP_AJAX)) {
			$class = "visible_au_chargement";
		} else {
			$class = "invisible_au_chargement";
		}


		if($sousmenu) {
			//offset is not necessary when javascript is active. It can be usefull when js is disabled
      $offset = (int)round($decal-$coeff_decalage*max(0,($decal+count($sousmenu)*$largitem_moy-$largeur_maxi_menu)));
			if ($offset<0){	$offset = 0; }

			$width=0;
			$max_width=0;
			foreach($sousmenu as $souspage => $sousdetail) {
				if ($width+1.25*$largitem_moy>$largeur_maxi_menu){
          $res .= "</ul><ul>\n";
          if($width>$max_width) $max_width=$width;
          $width=0;
        }
				$largitem = 0;
				if($souspage=='espacement') {
					if ($width>0){
						$res .= "<li class='separateur' style='width:20px;'></li>\n";
					}
				} else {
				  list($html,$largitem) = icone_bandeau_secondaire (_T($sousdetail->libelle),
				    bandeau_creer_url($sousdetail->url?$sousdetail->url:$souspage, $sousdetail->urlArg),
				    $sousdetail->icone, $souspage, $sous_rubrique
				  );
				  $res .= $html;
				}
				$width+=$largitem+10;
				if($width>$max_width) $max_width+=$largitem;
			}
			$res .= "</ul></div>\n";
			$res = "<div class='bandeau_sec h-list' style='width:{$max_width}px;'><ul>".$res;
		}

	return $res;
}
*/
/*
function icone_bandeau_secondaire($texte, $lien, $fond, $rubrique_icone = "vide", $rubrique, $aide=""){
	global $spip_display;
	global $menu_accesskey, $compteur_survol;

	$alt = '';
	$title = '';
	$accesskey = '';
	if ($spip_display == 1) {
		//$hauteur = 20;
		$largeur = 80;
	}
	else if ($spip_display == 3){
		//$hauteur = 26;
		$largeur = 40;
		$title = "title=\"$texte\"";
		$alt = $texte;
	}
	else {
		//$hauteur = 68;
		if (count(explode(" ", $texte)) > 1) $largeur = 80;
		else $largeur = 70;
		$alt = "";
	}
	if ($aide AND $spip_display != 3) {
		$largeur += 50;
		//$texte .= aide($aide);
	}
	if ($spip_display != 3 AND strlen($texte)>16) $largeur += 20;

	if (!$menu_accesskey = intval($menu_accesskey)) $menu_accesskey = 1;
	if ($menu_accesskey < 10) {
		$accesskey = " accesskey='$menu_accesskey'";
		$menu_accesskey++;
	}
	else if ($menu_accesskey == 10) {
		$accesskey = " accesskey='0'";
		$menu_accesskey++;
	}
	if ($spip_display == 3) $accesskey_icone = $accesskey;

	$class_select = " class='menu-item".($rubrique_icone != $rubrique ? "" : " selection")."'";
	$compteur_survol ++;

	$a_href = "<a$accesskey href=\"$lien\"$class_select>";

	if ($spip_display != 1) {
		$res = "<li class='cellule36' style='width: ".$largeur."px;'>";
		$res .= $a_href .
		  http_img_pack("$fond", $alt, "$title");
		if ($aide AND $spip_display != 3) $res .= aide($aide)." ";
		if ($spip_display != 3) {
			$res .= "<span>$texte</span>";
		}
		$res .= "</a></li>\n";
	}
	else $res = "<li style='width: $largeur" . "px' class='cellule-texte'>$a_href". $texte . "</a></li>\n";

	return array($res, $largeur);
}
*/

function inc_bandeau_dist($rubrique, $sous_rubrique, $largeur)
{
	return "<div id='bando_haut'>"
		. bando_identite()
		. bando_navigation(definir_barre_boutons())
		. "</div>"
		;
	
}
?>
