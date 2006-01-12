<?php
/**
 * definition de la barre de boutons et menus en haut des pages de
 * l'interface privée
 */

global $boutons_admin;

$boutons_admin=array(
	'asuivre' => array(
		'icone' => 'asuivre-48.png',
		'libelle' => 'icone_a_suivre',
	),
	'naviguer' => array(
		'icone' => "documents-48$spip_lang_rtl.png",
		'libelle' => 'icone_edition_site',
	),
	'forum_admin' => array(
		'icone' => 'messagerie-48.png',
		'libelle' => 'titre_forum',
	),
	'auteurs' => array(
		'icone' => 'redacteurs-48.png',
		'libelle' => 'icone_auteurs',
		'menu' => null,
	)
);

if ($GLOBALS['connect_statut'] == "0minirezo"
	AND $GLOBALS['meta']["activer_statistiques"] != 'non') {
	$boutons_admin['statistiques_visites']=array(
		'icone' => 'statistiques-48.png',
		'libelle' => 'icone_statistiques_visites',
	);
}
if ($GLOBALS['connect_statut'] == '0minirezo'
	AND $GLOBALS['connect_toutes_rubriques']) {
	$boutons_admin['configuration']=array(
		'icone' => 'administration-48.png',
		'libelle' => 'icone_configuration_site',
	);
}
$boutons_admin[]='espacement';
$boutons_admin['aide_index']=array(
		'icone' => 'aide-48'.lang_dir($spip_lang,$spip_lang_rtl).'.png',
		'libelle' => 'icone_aide_ligne',
		'url' => "javascript:window.open('aide_index.php3?var_lang=$spip_lang', 'aide_spip', 'scrollbars=yes,resizable=yes,width=740,height=580');",
		'url2' => "aide_index.php3?var_lang=$spip_lang",
		'target' => 'aide_spip'
);
$boutons_admin['visiter']=array(
		'icone' => "visiter-48$spip_lang_rtl.png",
		'libelle' => 'icone_visiter_site',
		'url' => '/'
);

// les sous menu des boutons, que si on est admin
if ($GLOBALS['connect_statut'] == '0minirezo'
	AND $GLOBALS['connect_toutes_rubriques']) {

	// sous menu edition

	$sousmenu=array();

	$nombre_articles = spip_num_rows(spip_query("SELECT art.id_article FROM spip_articles AS art, spip_auteurs_articles AS lien WHERE lien.id_auteur = '$connect_id_auteur' AND art.id_article = lien.id_article LIMIT 1"));
	if ($nombre_articles > 0) {
		$sousmenu['articles_page']=array(
			'libelle' => "icone_tous_articles",
			'icone' => "article-24.gif");
	}

	$activer_breves=$GLOBALS['meta']["activer_breves"];
	if ($activer_breves != "non"){
		$sousmenu['breves']=array(
			'libelle' => "icone_breves",
			'icone' => "breve-24.gif");
	}

	if ($options == "avancees"){
		$articles_mots = $GLOBALS['meta']['articles_mots'];
		if ($articles_mots != "non") {
			$sousmenu['breves']=array(
				'libelle' => "icone_mots_cles",
				'icone' => "mot-cle-24.gif");
		}

		$activer_sites = $GLOBALS['meta']['activer_sites'];
		if ($activer_sites<>'non')
			$sousmenu['sites_tous']=array(
				'libelle' => "icone_sites_references",
				'icone' => "site-24.gif");

		if (@spip_num_rows(spip_query("SELECT * FROM spip_documents_rubriques LIMIT 1")) > 0) {
			$sousmenu['documents_liste']=array(
				'libelle' => "icone_doc_rubrique",
				'icone' => "doc-24.gif");
		}
	}
	$boutons_admin['edition']['sousmenu']= $sousmenu;
	
	// sous menu forum

	$sousmenu=array();

	if ($GLOBALS['meta']['forum_prive_admin'] == 'oui')
		$sousmenu['forum_admin']=array(
			'libelle' => "icone_forum_administrateur",
			'icone' => "forum-admin-24.gif");

	$sousmenu['controle_forum']=array(
		'libelle' => "icone_suivi_forums",
		'icone' => "suivi-forum-24.gif");
	$sousmenu['controle_petition']=array(
		'libelle' => "icone_suivi_pettions",
		'icone' => "suivi-petition-24.gif");
	
	$boutons_admin['forum_admin']['sousmenu']= $sousmenu;

	// sous menu auteurs

	$boutons_admin['auteurs']['sousmenu']= array(
		'auteurs_edit' => array(
			'libelle' => "icone_informations_personnelles",
			'icone' => "fiche-perso-24.gif"
		),
		'auteur_infos' => array(
			'libelle' => "icone_creer_nouvel_auteur",
			'icone' => "auteur-24.gif"
		)
	);

	// sous menu statistiques
	
	$sousmenu=array();
	$sousmenu['statistiques_repartition']=array(
		'libelle' => "icone_repartition_visites",
		'icone' => "rubrique-24.gif");

	if ($GLOBALS['meta']['multi_articles'] == 'oui'
		OR $GLOBALS['meta']['multi_rubriques'] == 'oui')
		$sousmenu['statistiques_lang']=array(
			'libelle' => "onglet_repartition_lang",
			'icone' => "langues-24.gif");

	$sousmenu['statistiques_referers']=array(
		'libelle' => "titre_liens_entrants",
		'icone' => "referers-24.gif");

	$boutons_admin['statistiques']['sousmenu']= $sousmenu;
	
	// sous menu configuration

	$sousmenu=array();

	$sousmenu['config-lang']=array(
		'libelle' => "icone_gestion_langues",
		'icone' => "langues-24.gif");

	if ($options == "avancees") {
		$sousmenu['admin_tech']=array(
			'libelle' => "icone_maintenance_site",
			'icone' => "base-24.gif");
		$sousmenu['admin_vider']=array(
			'libelle' => "onglet_vider_cache",
			'icone' => "cache-24.gif");
	} else {
		$sousmenu['admin_tech']=array(
			'libelle' => "icone_sauver_site",
			'icone' => "base-24.gif");
	}
} // si admin


function debut_page_bis($titre = "", $rubrique = "asuivre", $sous_rubrique = "asuivre", $onLoad = "", $css="") {

	init_entete($titre, $rubrique, $onLoad, $css);
	init_body_bis($rubrique, $sous_rubrique);
}

// fonction envoyant la double serie d'icones de redac
// version utilisant le tableau ci dessus
function init_body_bis($rubrique = "asuivre", $sous_rubrique = "asuivre") {
	global $couleur_foncee;
	global $couleur_claire;
	global $REQUEST_URI, $HTTP_HOST;
	global $connect_id_auteur;
	global $connect_statut;
	global $connect_activer_messagerie;
	global $connect_toutes_rubriques;
	global $auth_can_disconnect, $connect_login;
	global $options, $spip_display, $spip_ecran;
	global $spip_lang, $spip_lang_rtl, $spip_lang_left, $spip_lang_right;
	$activer_messagerie = "oui";

	$adresse_site = $GLOBALS['meta']["adresse_site"];
	if (!$adresse_site) {
			$adresse_site = "http://$HTTP_HOST".substr($REQUEST_URI, 0, strpos($REQUEST_URI, "/" . _DIR_RESTREINT_ABS));
			ecrire_meta("adresse_site", $adresse_site);
			ecrire_metas();
	}

	if ($spip_ecran == "large") $largeur = 974;
	else $largeur = 750;

	if (strlen($adresse_site)<10) _DIR_RACINE;

	$link = new Link;
	echo "\n<map name='map_layout'>";
	echo lien_change_var ($link, 'set_disp', 1, '1,0,18,15', _T('lien_afficher_texte_seul'), "onMouseOver=\"changestyle('bandeauvide','visibility', 'visible');\"");
	echo lien_change_var ($link, 'set_disp', 2, '19,0,40,15', _T('lien_afficher_texte_icones'), "onMouseOver=\"changestyle('bandeauvide','visibility', 'visible');\"");
	echo lien_change_var ($link, 'set_disp', 3, '41,0,59,15', _T('lien_afficher_icones_seuls'), "onMouseOver=\"changestyle('bandeauvide','visibility', 'visible');\"");
	echo "\n</map>";



if ($spip_display == "4") {
	// Icones principales
	echo "<ul>";
	echo "<li><a href='./'>"._T('icone_a_suivre')."</a>";
	echo "<li><a href='" . generer_url_ecrire("naviguer") . "'>"._T('icone_edition_site')."</a>";
	echo "<li><a href='" . generer_url_ecrire("forum_admin"). "'>"._T('titre_forum')."</a>";
	echo "<li><a href='" . generer_url_ecrire("auteurs") . "'>"._T('icone_auteurs')."</a>";
	echo "<li><a href=\"$adresse_site/\">"._T('icone_visiter_site')."</a>";
	echo "</ul>";
} else {

	// iframe permettant de passer les changements de statut rapides
	echo "<iframe id='iframe_action' name='iframe_action' width='1' height='1' style='position: absolute; visibility: hidden;'></iframe>";

	// Lien oo
	echo "<div class='invisible_au_chargement' style='position: absolute; height: 0px; visibility: hidden;'><a href='oo'>"._T("access_mode_texte")."</a></div>";
	
	echo "<div id='haut-page'>";

	// Icones principales
	echo "<div class='bandeau-principal' align='center'>\n";
	echo "<div class='bandeau-icones'>\n";
	echo "<table width='$largeur' cellpadding='0' cellspacing='0' border='0' align='center'><tr>\n";

	foreach($GLOBALS['boutons_admin'] as $page => $detail) {
		if($page=='vide') {
			echo "<td> &nbsp; </td>";
		} else {
			icone_bandeau_principal (_T($detail['libelle']),
				generer_url_ecrire($page),
				$detail['icone'], $page, $rubrique, "", $page, $sous_rubrique);
		}
	}
	echo "</tr></table>\n";

	echo "</div>\n";

	echo "<table width='$largeur' cellpadding='0' cellspacing='0' align='center'><tr><td>";
	echo "<div style='text-align: $spip_lang_left; width: ".$largeur."px; position: relative; z-index: 2000;'>";

	// Icones secondaires

	$decal=0;
	$activer_messagerie = "oui";
	$connect_activer_messagerie = "oui";

	foreach($GLOBALS['boutons_admin'] as $page => $detail) {
		if ($rubrique == $page){
			$class = "visible_au_chargement";
		} else {
			$class = "invisible_au_chargement";
		}

		$sousmenu= $detail['sousmenu'];
		if($sousmenu) {
			echo "<div class='$class' id='bandeaudocuments' style='position: absolute; $spip_lang_left: ".$decal."px;'><div class='bandeau_sec'><table class='gauche'><tr>\n";
		
			foreach($sousmenu as $souspage => $sousdetail) {
				icone_bandeau_secondaire (_T($sousdetail['libelle']), generer_url_ecrire($souspage,""), $sousdetail['icone'], $souspage, $sous_rubrique);
			}
			echo "</tr></table></div></div>";
		}
		
		$decal = largeur_icone_bandeau_principal(_T($detail['libelle']));
	}

	// Refermer tout de suite le bandeau deroule par defaut
	echo "
	<script type='text/javascript'><!--
		changestyle('-', '-', '-');
	// --></script>\n";

	echo "</div>";
	
	echo "</td></tr></table>";
	
	echo "</div>\n";

	//
	// Bandeau colore
	//

if (true /*$bandeau_colore*/) {
	if ($rubrique == "administration") {
		$style = "background: url(" . _DIR_IMG_PACK . "rayures-danger.png); background-color: $couleur_foncee";
		echo "<style>a.icone26 { color: white; }</style>";
	}
	else {
		$style = "background-color: $couleur_claire";
	}

	echo "\n<div style=\"max-height: 40px; width: 100%; border-bottom: solid 1px white;$style\">";
	echo "<table align='center' cellpadding='0' background='' width='$largeur'><tr width='$largeur'>";

	echo "<td valign='middle' class='bandeau_couleur' style='text-align: $spip_lang_left;'>";
//		echo "<a href='" . generer_url_ecrire("articles_tous","") . "' class='icone26' onMouseOver=\"changestyle('bandeautoutsite','visibility','visible');\">" .
//		  http_img_pack("tout-site.png", "", "width='26' height='20' border='0'") . "</a>";

		$id_rubrique = $GLOBALS['id_rubrique'];
		echo "<a href='" . generer_url_ecrire("articles_tous","") . "' class='icone26' onMouseOver=\"changestyle('bandeautoutsite','visibility','visible'); charger_id_url_si_vide('ajax_page.php?fonction=aff_nav_recherche&id=$id_rubrique','nav-recherche');\">" .
		  http_img_pack("tout-site.png", "", "width='26' height='20' border='0'") . "</a>";
		if ($id_rubrique > 0) echo "<a href='" . generer_url_ecrire("brouteur","id_rubrique=$id_rubrique") . "' class='icone26' onMouseOver=\"changestyle('bandeaunavrapide','visibility','visible');\">" .
		  http_img_pack("naviguer-site.png", "", "width='26' height='20' border='0'") ."</a>";
		else echo "<a href='" . generer_url_ecrire("brouteur","") . "' class='icone26' onMouseOver=\"changestyle('bandeaunavrapide','visibility','visible');\" >" .
		  http_img_pack("naviguer-site.png", "", "width='26' height='20' border='0'") . "</a>";

		echo "<a href='" . generer_url_ecrire("recherche","") . "' class='icone26' onMouseOver=\"changestyle('bandeaurecherche','visibility','visible'); findObj('form_recherche').focus();\" >" .
		  http_img_pack("loupe.png", "", "width='26' height='20' border='0'") ."</a>";

		echo http_img_pack("rien.gif", " ", "width='10'");

		echo "<a href='" . generer_url_ecrire("calendrier","type=semaine") . "' class='icone26' onMouseOver=\"changestyle('bandeauagenda','visibility','visible');\">" .
		  http_img_pack("cal-rv.png", "", "width='26' height='20' border='0'") ."</a>";
		echo "<a href='" . generer_url_ecrire("messagerie","") . "' class='icone26' onMouseOver=\"changestyle('bandeaumessagerie','visibility','visible');\">" .
		  http_img_pack("cal-messagerie.png", "", "width='26' height='20' border='0'") ."</a>";
		echo "<a href='" . generer_url_ecrire("synchro","") . "' class='icone26' onMouseOver=\"changestyle('bandeausynchro','visibility','visible');\">" .
		  http_img_pack("cal-suivi.png", "", "width='26' height='20' border='0'") . "</a>";
		

		if (!($connect_statut == "0minirezo" AND $connect_toutes_rubriques)) {
			echo http_img_pack("rien.gif", " ", "width='10'");
			echo "<a href='" . generer_url_ecrire("auteurs_edit","id_auteur=$connect_id_auteur") . "' class='icone26' onMouseOver=\"changestyle('bandeauinfoperso','visibility','visible');\">" .
			  http_img_pack("fiche-perso.png", "", "border='0' onMouseOver=\"changestyle('bandeauvide','visibility', 'visible');\"");
			echo "</a>";
		}
		
	echo "</td>";
	echo "<td valign='middle' class='bandeau_couleur' style='text-align: $spip_lang_left;'>";
		// overflow pour masquer les noms tres longs (et eviter debords, notamment en ecran etroit)
		if ($spip_ecran == "large") $largeur_nom = 300;
		else $largeur_nom= 110;
		echo "<div style='width: ".$largeur_nom."px; height: 14px; overflow: hidden;'>";
		// Redacteur connecte
		echo typo($GLOBALS["connect_nom"]);
		echo "</div>";
	
	echo "</td>";

	echo "<td> &nbsp; </td>";


	echo "<td class='bandeau_couleur' style='text-align: $spip_lang_right;' valign='middle'>";

			// Choix display
		//	echo"<img src=_DIR_IMG_PACK . 'rien.gif' width='10' />";
			if ($options != "avancees") {
				$lien = new Link;
				$lien->addVar('set_options', 'avancees');
				$simple = "<b>"._T('icone_interface_simple')."</b>/<a href='".$lien->getUrl()."' class='lien_sous'>"._T('icone_interface_complet')."</a>";
				$icone = "interface-display-comp.png";
			} else {
				$lien = new Link;
				$lien->addVar('set_options', 'basiques');
				$simple = "<a href='".$lien->getUrl()."' class='lien_sous'>"._T('icone_interface_simple')."</a>/<b>"._T('icone_interface_complet')."</b>";
				$icone = "interface-display.png";
			}
			echo "<a href='". $lien->getUrl() ."' class='icone26' onMouseOver=\"changestyle('bandeaudisplay','visibility', 'visible');\">" .
			  http_img_pack("$icone", "", "width='26' height='20' border='0'")."</a>";

			echo http_img_pack("rien.gif", " ", "width='10' height='1'");
			echo http_img_pack("choix-layout$spip_lang_rtl".($spip_lang=='he'?'_he':'').".png", "abc", "class='format_png' valign='middle' width='59' height='15' usemap='#map_layout' border='0'");


			echo http_img_pack("rien.gif", " ", "width='10' height='1'");
			// grand ecran
			$lien = new Link;
			if ($spip_ecran == "large") {
				$lien->addVar('set_ecran', 'etroit');
				$i = _T('info_petit_ecran');
				echo "<a href='". $lien->getUrl() ."' class='icone26' onMouseOver=\"changestyle('bandeauecran','visibility', 'visible');\" title=\"$i\">" .
				  http_img_pack("set-ecran-etroit.png", $i, "width='26' height='20' border='0'") . "</a>";
				$ecran = "<div><a href='".$lien->getUrl()."' class='lien_sous'>"._T('info_petit_ecran')."</a>/<b>"._T('info_grand_ecran')."</b></div>";
			}
			else {
				$lien->addVar('set_ecran', 'large');
				$i = _T('info_grand_ecran');
				echo "<a href='". $lien->getUrl() ."' class='icone26' onMouseOver=\"changestyle('bandeauecran','visibility', 'visible');\" title=\"$i\">" .
				  http_img_pack("set-ecran.png", $i, "width='26' height='20' border='0'") ."</a>";
				$ecran = "<div><b>"._T('info_petit_ecran')."</b>/<a href='".$lien->getUrl()."' class='lien_sous'>"._T('info_grand_ecran')."</a></div>";
			}

		echo "</td>";
		
		echo "<td class='bandeau_couleur' style='width: 60px; text-align:$spip_lang_left;' valign='middle'>";
		choix_couleur();
		
		echo "</td>";
	//
	// choix de la langue
	//
	if ($GLOBALS['all_langs']) {
		echo "<td class='bandeau_couleur' style='width: 100px; text-align: $spip_lang_right;' valign='middle'>";
		echo menu_langues('var_lang_ecrire');
		echo "</td>";
	}

		echo "<td class='bandeau_couleur' style='text-align: $spip_lang_right; width: 28px;' valign='middle'>";

			if ($auth_can_disconnect) {	
				echo "<a href='" . generer_url_public("spip_cookie","logout=$connect_login") . "' class='icone26' onMouseOver=\"changestyle('bandeaudeconnecter','visibility', 'visible');\">" .
				  http_img_pack("deconnecter-24.gif", "", "border='0'") . "</a>";
			}
		echo "</td>";
	
	
	echo "</tr></table>";

} // fin bandeau colore

	//
	// Barre des gadgets
	// (elements invisibles qui s'ouvrent sous la barre precedente)
	//

// debut des gadgets
if (true /*$gadgets*/) {

	echo "<table width='$largeur' cellpadding='0' cellspacing='0' align='center'><tr><td>";


	// GADGET Menu rubriques
	echo "<div style='position: relative; z-index: 1000;'>";
	echo "<div id='bandeautoutsite' class='bandeau_couleur_sous' style='$spip_lang_left: 0px;'>";
	echo "<a href='" . generer_url_ecrire("articles_tous","") . "' class='lien_sous'>"._T('icone_site_entier')."</a>";
	echo "<img src='"._DIR_IMG_PACK."searching.gif' id='img_nav-recherche' style='border:0px; visibility: hidden' />";
	afficher_menu_rubriques();

//	echo "<div id='nav-recherche' style='width: 450px; visibility: hidden;'></div>";
	echo "</div>";
	// FIN GADGET Menu rubriques
	
	
	
	
	// GADGET Navigation rapide
	echo "<div id='bandeaunavrapide' class='bandeau_couleur_sous' style='$spip_lang_left: 30px; width: 300px;'>";

	if ($id_rubrique > 0) echo "<a href='" . generer_url_ecrire("brouteur","id_rubrique=$id_rubrique") . "' class='lien_sous'>";
	else echo "<a href='" . generer_url_ecrire("brouteur","") . "' class='lien_sous'>";
	echo _T('icone_brouteur');
	echo "</a>";

	$gadget = '';
		$vos_articles = spip_query("SELECT articles.id_article, articles.titre, articles.statut FROM spip_articles AS articles, spip_auteurs_articles AS lien WHERE articles.id_article=lien.id_article ".
			"AND lien.id_auteur=$connect_id_auteur AND articles.statut='prepa' ORDER BY articles.date DESC LIMIT 5");
		if (spip_num_rows($vos_articles) > 0) {
			$gadget .= "<div>&nbsp;</div>";
			$gadget .= "<div class='bandeau_rubriques' style='z-index: 1;'>";
			$gadget .= bandeau_titre_boite2(afficher_plus(generer_url_ecrire("articles_page",""))._T('info_en_cours_validation'), "article-24.gif", '', '', false);
			$gadget .= "\n<div class='plan-articles'>\n";
			while($row = spip_fetch_array($vos_articles)) {
				$id_article = $row['id_article'];
				$titre = typo(sinon($row['titre'], _T('ecrire:info_sans_titre')));
				$statut = $row['statut'];
				$gadget .= "<a class='$statut' style='font-size: 10px;' href='" . generer_url_ecrire("articles","id_article=$id_article") . "'>$titre</a>\n";
			}
			$gadget .= "</div>";
			$gadget .= "</div>";
		}
	
		$vos_articles = spip_query("SELECT articles.id_article, articles.titre, articles.statut FROM spip_articles AS articles WHERE articles.statut='prop' ".
			" ORDER BY articles.date DESC LIMIT 5");
		if (spip_num_rows($vos_articles) > 0) {
			$gadget .= "<div>&nbsp;</div>";
			$gadget .= "<div class='bandeau_rubriques' style='z-index: 1;'>";
			$gadget .= bandeau_titre_boite2(afficher_plus('./')._T('info_articles_proposes'), "article-24.gif", '', '', false);
			$gadget .= "<div class='plan-articles'>";
			while($row = spip_fetch_array($vos_articles)) {
				$id_article = $row['id_article'];
				$titre = sinon($row['titre'], _T('ecrire:info_sans_titre'));
				$statut = $row['statut'];
	
				$gadget .= "<a class='$statut' style='font-size: 10px;' href='" . generer_url_ecrire("articles","id_article=$id_article") . "'>$titre</a>";
			}
			$gadget .= "</div>";
			$gadget .= "</div>";
		}
			
		$vos_articles = spip_query("SELECT * FROM spip_breves WHERE statut='prop' ".
			" ORDER BY date_heure DESC LIMIT 5");
		if (spip_num_rows($vos_articles) > 0) {
			$gadget .= "<div>&nbsp;</div>";
			$gadget .= "<div class='bandeau_rubriques' style='z-index: 1;'>";
			$gadget .= bandeau_titre_boite2(afficher_plus(generer_url_ecrire("breves",""))._T('info_breves_valider'), "breve-24.gif", "$couleur_foncee", "white", false);
			$gadget .= "<div class='plan-articles'>";
			while($row = spip_fetch_array($vos_articles)) {
				$id_breve = $row['id_breve'];
				$titre = typo(sinon($row['titre'], _T('ecrire:info_sans_titre')));
				$statut = $row['statut'];
	
				$gadget .= "<a class='$statut' style='font-size: 10px;' href='" . generer_url_ecrire("breves_voir","id_breve=$id_breve") . "'>$titre</a>";
			}
			$gadget .= "</div>";
			$gadget .= "</div>";
		}


		$query = "SELECT id_rubrique FROM spip_rubriques LIMIT 1";
		$result = spip_query($query);
		
		if (spip_num_rows($result) > 0) {
			$gadget .= "<div>&nbsp;</div>";
			$id_rubrique = $GLOBALS['id_rubrique'];
			if ($id_rubrique > 0) {
				$dans_rub = "&id_rubrique=$id_rubrique";
				$dans_parent = "&id_parent=$id_rubrique";
			}
			if ($connect_statut == "0minirezo") {	
				$gadget .= "<div style='width: 140px; float: $spip_lang_left;'>";
				if ($id_rubrique > 0)
					$gadget .= icone_horizontale(_T('icone_creer_sous_rubrique'), generer_url_ecrire("rubriques_edit","new=oui$dans_parent"), "rubrique-24.gif", "creer.gif", false);
				else 
					$gadget .= icone_horizontale(_T('icone_creer_rubrique'), generer_url_ecrire("rubriques_edit","new=oui"), "rubrique-24.gif", "creer.gif", false);
				$gadget .= "</div>";
			}		
			$gadget .= "<div style='width: 140px; float: $spip_lang_left;'>";
			$gadget .= icone_horizontale(_T('icone_ecrire_article'), generer_url_ecrire("articles_edit","new=oui$dans_rub"), "article-24.gif","creer.gif", false);
			$gadget .= "</div>";
			
			$activer_breves = $GLOBALS['meta']["activer_breves"];
			if ($activer_breves != "non") {
				$gadget .= "<div style='width: 140px;  float: $spip_lang_left;'>";
				$gadget .= icone_horizontale(_T('icone_nouvelle_breve'), generer_url_ecrire("breves_edit","new=oui$dans_rub"), "breve-24.gif","creer.gif", false);
				$gadget .= "</div>";
			}
			
			if ($GLOBALS['meta']["activer_sites"] == 'oui') {
				if ($connect_statut == '0minirezo' OR $GLOBALS['meta']["proposer_sites"] > 0) {
					$gadget .= "<div style='width: 140px; float: $spip_lang_left;'>";
					$gadget .= icone_horizontale(_T('info_sites_referencer'), generer_url_ecrire("sites_edit","new=oui$dans_parent&target=" . generer_url_ecrire("sites")), "site-24.gif","creer.gif", false);
					$gadget .= "</div>";
				}
			}
			
		}

		$gadget .= "</div>";

	echo afficher_javascript($gadget);
	// FIN GADGET Navigation rapide


	// GADGET Recherche
		echo "<div id='bandeaurecherche' class='bandeau_couleur_sous' style='width: 146px; $spip_lang_left: 60px;'>";
		global $recherche;
				$recherche_aff = _T('info_rechercher');
			//	$onfocus = "onfocus=this.value='';";
			echo "<form method='get' style='margin: 0px; position: relative;' action='" . generer_url_ecrire("recherche","") . "'>";
			

			
			echo "<input type=\"search\" id=\"form_recherche\" style=\"width: 140px;\" size=\"10\" value='$recherche_aff' name=\"recherche\" onkeypress=\"t=window.setTimeout('lancer_recherche(\'form_recherche\',\'resultats_recherche\')', 200);\" autocomplete=\"off\" class=\"formo\" accesskey=\"r\" ".$onfocus.">";
			echo "</form>";
		echo "</div>";
	// FIN GADGET recherche


	// GADGET Agenda
	$gadget = '';
		$today = getdate(time());
		$jour_today = $today["mday"];
		$mois_today = $today["mon"];
		$annee_today = $today["year"];
		$date = date("Y-m-d", mktime(0,0,0,$mois_today, 1, $annee_today));
		$mois = mois($date);
		$annee = annee($date);
		$jour = jour($date);
	
		// Taches (ne calculer que la valeur booleenne...)
		if (spip_num_rows(spip_query("SELECT type FROM spip_messages AS messages WHERE id_auteur=$connect_id_auteur AND statut='publie' AND type='pb' AND rv!='oui' LIMIT 1")) OR
		    spip_num_rows(spip_query("SELECT type FROM spip_messages AS messages, spip_auteurs_messages AS lien WHERE ((lien.id_auteur='$connect_id_auteur' AND lien.id_message=messages.id_message) OR messages.type='affich') AND messages.rv='oui' AND messages.date_heure > DATE_SUB(NOW(), INTERVAL 1 DAY) AND messages.date_heure < DATE_ADD(NOW(), INTERVAL 1 MONTH) AND messages.statut='publie' GROUP BY messages.id_message ORDER BY messages.date_heure LIMIT 1"))) {
			$largeur = "410px";
			$afficher_cal = true;
		}
		else {
			$largeur = "200px";
			$afficher_cal = false;
		}



		// Calendrier
			$gadget .= "<div id='bandeauagenda' class='bandeau_couleur_sous' style='width: $largeur; $spip_lang_left: 100px;'>";
			$gadget .= "<a href='" . generer_url_ecrire("calendrier","type=semaine") . "' class='lien_sous'>";
			$gadget .= _T('icone_agenda');
			$gadget .= "</a>";
			
			$gadget .= "<table><tr>";
			$gadget .= "<td valign='top' width='200'>";
				$gadget .= "<div>";
				$gadget .= http_calendrier_agenda($annee_today, $mois_today, $jour_today, $mois_today, $annee_today, false, generer_url_ecrire('calendrier'));
				$gadget .= "</div>";
				$gadget .= "</td>";
				if ($afficher_cal) {
					$gadget .= "<td valign='top' width='10'> &nbsp; </td>";
					$gadget .= "<td valign='top' width='200'>";
					$gadget .= "<div>&nbsp;</div>";
					$gadget .= "<div style='color: black;'>";
					$gadget .=  http_calendrier_rv(sql_calendrier_taches_annonces(),"annonces");
					$gadget .=  http_calendrier_rv(sql_calendrier_taches_pb(),"pb");
					$gadget .=  http_calendrier_rv(sql_calendrier_taches_rv(), "rv");
					$gadget .= "</div>";
					$gadget .= "</td>";
				}
			
			$gadget .= "</tr></table>";
			$gadget .= "</div>";
	echo afficher_javascript($gadget);
	// FIN GADGET Agenda


	// GADGET Messagerie
	$gadget = '';
		$gadget .= "<div id='bandeaumessagerie' class='bandeau_couleur_sous' style='$spip_lang_left: 130px; width: 200px;'>";
		$gadget .= "<a href='" . generer_url_ecrire("messagerie","") . "' class='lien_sous'>";
		$gadget .= _T('icone_messagerie_personnelle');
		$gadget .= "</a>";
		
		$gadget .= "<div>&nbsp;</div>";
		$gadget .= icone_horizontale(_T('lien_nouvea_pense_bete'),generer_url_ecrire("message_edit","new=oui&type=pb"), "pense-bete.gif", '', false);
		$gadget .= icone_horizontale(_T('lien_nouveau_message'),generer_url_ecrire("message_edit","new=oui&type=normal"), "message.gif", '', false);
		if ($connect_statut == "0minirezo") {
		  $gadget .= icone_horizontale(_T('lien_nouvelle_annonce'),generer_url_ecrire("message_edit","new=oui&type=affich"), "annonce.gif", '', false);
		}
		$gadget .= "</div>";

	echo afficher_javascript($gadget);

	// FIN GADGET Messagerie


		// Suivi activite	
		echo "<div id='bandeausynchro' class='bandeau_couleur_sous' style='$spip_lang_left: 160px;'>";
		echo "<a href='" . generer_url_ecrire("synchro","") . "' class='lien_sous'>";
		echo _T('icone_suivi_activite');
		echo "</a>";
		echo "</div>";
	
		// Infos perso
		echo "<div id='bandeauinfoperso' class='bandeau_couleur_sous' style='width: 200px; $spip_lang_left: 200px;'>";
		echo "<a href='" . generer_url_ecrire("auteurs_edit","id_auteur=$connect_id_auteur") . "' class='lien_sous'>";
		echo _T('icone_informations_personnelles');
		echo "</a>";
		echo "</div>";

		
		//
		// -------- Affichage de droite ----------
	
		// Deconnection
		echo "<div class='bandeau_couleur_sous' id='bandeaudeconnecter' style='$spip_lang_right: 0px;'>";
		echo "<a href='" . generer_url_public("spip_cookie","logout=$connect_login") . "' class='lien_sous'>"._T('icone_deconnecter')."</a>".aide("deconnect");
		echo "</div>";
	
		$decal = 0;
		$decal = $decal + 150;

		echo "<div id='bandeauinterface' class='bandeau_couleur_sous' style='$spip_lang_right: ".$decal."px; text-align: $spip_lang_right;'>";
			echo _T('titre_changer_couleur_interface');
		echo "</div>";
		
		$decal = $decal + 70;
		
		echo "<div id='bandeauecran' class='bandeau_couleur_sous' style='width: 200px; $spip_lang_right: ".$decal."px; text-align: $spip_lang_right;'>";
			echo $ecran;
		echo "</div>";
		
		$decal = $decal + 110;
		
		// En interface simplifiee, afficher un permanence l'indication de l'interface
		if ($options != "avancees") {
			echo "<div id='displayfond' class='bandeau_couleur_sous' style='$spip_lang_right: ".$decal."px; text-align: $spip_lang_right; visibility: visible; background-color: white; color: $couleur_foncee; z-index: -1000; border: 1px solid $couleur_claire; border-top: 0px;'>";
				echo "<b>"._T('icone_interface_simple')."</b>";
			echo "</div>";
		}
		echo "<div id='bandeaudisplay' class='bandeau_couleur_sous' style='$spip_lang_right: ".$decal."px; text-align: $spip_lang_right;'>";
			echo $simple;

			if ($options != "avancees") {		
				echo "<div>&nbsp;</div><div style='width: 250px; text-align: $spip_lang_left;'>"._T('texte_actualite_site_1')."<a href='./?set_options=avancees'>"._T('texte_actualite_site_2')."</a>"._T('texte_actualite_site_3')."</div>";
			}

		echo "</div>";
	
	
	echo "</div>";
	echo "</td></tr></table>";

} // fin des gadgets

	echo "</div>";
	echo "</div>";

	if ($options != "avancees") echo "<div style='height: 18px;'>&nbsp;</div>";
	
}

	// Ouverture de la partie "principale" de la page
	// Petite verif pour ne pas fermer le formulaire de recherche pendant qu'on l'edite	
	echo "<center onMouseOver=\"if (findObj('bandeaurecherche') && findObj('bandeaurecherche').style.visibility == 'visible') { ouvrir_recherche = true; } else { ouvrir_recherche = false; } changestyle('bandeauvide', 'visibility', 'hidden'); if (ouvrir_recherche == true) { changestyle('bandeaurecherche','visibility','visible'); }\">";

			$result_messages = spip_query("SELECT * FROM spip_messages AS messages, spip_auteurs_messages AS lien WHERE lien.id_auteur=$connect_id_auteur AND vu='non' AND statut='publie' AND type='normal' AND lien.id_message=messages.id_message");
			$total_messages = @spip_num_rows($result_messages);
			if ($total_messages == 1) {
				while($row = @spip_fetch_array($result_messages)) {
					$ze_message=$row['id_message'];
					echo "<div class='messages'><a href='" . generer_url_ecrire("message","id_message=$ze_message") . "'><font color='$couleur_foncee'>"._T('info_nouveau_message')."</font></a></div>";
				}
			}
			if ($total_messages > 1) echo "<div class='messages'><a href='" . generer_url_ecrire("messagerie","") . "'><font color='$couleur_foncee'>"._T('info_nouveaux_messages', array('total_messages' => $total_messages))."</font></a></div>";


	// Afficher les auteurs recemment connectes
	
	global $changer_config;
	global $activer_messagerie;
	global $activer_imessage;
	global $connect_activer_messagerie;
	global $connect_activer_imessage;

		if ($changer_config!="oui"){
			$activer_messagerie = "oui";
			$activer_imessage = "oui";
		}
	
			if ($activer_imessage != "non" AND ($connect_activer_imessage != "non" OR $connect_statut == "0minirezo")) {
				$query2 = "SELECT id_auteur, nom FROM spip_auteurs WHERE id_auteur!=$connect_id_auteur AND imessage!='non' AND en_ligne>DATE_SUB(NOW(),INTERVAL 15 MINUTE)";
				$result_auteurs = spip_query($query2);
				$nb_connectes = spip_num_rows($result_auteurs);
			}
				
			$flag_cadre = (($nb_connectes > 0) OR $rubrique == "messagerie");
			if ($flag_cadre) echo "<div class='messages' style='color: #666666;'>";

			
			if ($nb_connectes > 0) {
				if ($nb_connectes > 0) {
					echo "<b>"._T('info_en_ligne')."</b>";
					while ($row = spip_fetch_array($result_auteurs)) {
						$id_auteur = $row["id_auteur"];
						$nom_auteur = typo($row["nom"]);
						echo " &nbsp; ".bouton_imessage($id_auteur,$row)."&nbsp;<a href='" . generer_url_ecrire("auteurs_edit","id_auteur=$id_auteur") . "' style='color: #666666;'>$nom_auteur</a>";
					}
				}
			}
			if ($flag_cadre) echo "</div>";
}

?>
