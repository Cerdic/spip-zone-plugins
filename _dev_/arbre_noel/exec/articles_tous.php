<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

// http://doc.spip.org/@exec_articles_tous_dist
function exec_articles_tous_dist()
{
	$aff_art = _request('aff_art');
	$sel_lang = _request('sel_lang');

	global $article, $enfant, $text_article;
	global $connect_toutes_rubriques,$connect_id_auteur, $connect_statut;
	global $spip_dir_lang, $spip_lang, $browser_layer;
	
	changer_typo(); // pour definir $dir_lang
	if (!is_array($aff_art)) $aff_art = array('prop','publie');
	list($enfant, $first_couche, $last_couche) = arbo_articles_tous();

	if (($GLOBALS['meta']['multi_rubriques'] == 'oui' OR $GLOBALS['meta']['multi_articles'] == 'oui') AND $GLOBALS['meta']['gerer_trad'] == 'oui') 
		$langues = explode(',', $GLOBALS['meta']['langues_multilingue']);
	else	$langues = array();  

	$sel_lang[$spip_lang] = $spip_lang;

	if ($connect_statut == "0minirezo")
		$result = spip_query("SELECT id_article, titre, statut, id_rubrique, lang, id_trad, date_modif FROM spip_articles ORDER BY date DESC");
	else 
		$result = spip_query("SELECT articles.id_article, articles.titre, articles.statut, articles.id_rubrique, articles.lang, articles.id_trad, articles.date_modif FROM spip_articles AS articles, spip_auteurs_articles AS lien WHERE (articles.statut = 'publie' OR articles.statut = 'prop' OR (articles.statut = 'prepa' AND articles.id_article = lien.id_article AND lien.id_auteur = $connect_id_auteur)) GROUP BY id_article ORDER BY articles.date DESC");

	while($row = spip_fetch_array($result)) {
		$id_rubrique=$row['id_rubrique'];
		$id_article = $row['id_article'];
		$titre = typo($row['titre']);
		$statut = $row['statut'];
		$lang = $row['lang'];
		$id_trad = $row['id_trad'];
		$date_modif = $row['date_modif'];
		
		$aff_statut[$statut] = true; // signale qu'il existe de tels articles
		$text_article[$id_article]["titre"] = $titre;
		$text_article[$id_article]["statut"] = $statut;
		$text_article[$id_article]["lang"] = $lang;
		$text_article[$id_article]["id_trad"] = $id_trad;
		$text_article[$id_article]["date_modif"] = $date_modif;
		$GLOBALS['langues_utilisees'][$lang] = true;
		
		if (count($langues) > 1) {
			while (list(, $l) = each ($langues)) {
				if (in_array($l, $sel_lang)) $text_article[$id_article]["trad"]["$l"] =  "<span class='creer'>$l</span>";
			}
		}
		
		if ($id_trad == $id_article OR $id_trad == 0) {
			$text_article[$id_article]["trad"]["$lang"] = "<span class='lang_base'$spip_dir_lang>$lang</span>";
		}
		
		if (in_array($statut, $aff_art))
			$article[$id_rubrique][] = $id_article;
	}

	if ($text_article)
		foreach ($text_article as $id_article => $v) {
			$id_trad = $v["id_trad"];
			$lang = $v['lang'];
				
			
			if ($id_trad > 0 AND $id_trad != $id_article AND in_array($lang, $sel_lang)) {
				if ($text_article[$id_trad]["date_modif"] < $v["date_modif"]) 
					$c = 'foncee';
				else
					$c = 'claire';
				$text_article[$id_trad]["trad"][$lang] =
					"<a class='$c' href='" . generer_url_ecrire("articles","id_article=$id_article") . "'>$lang</a>";
			}
		}
	if (_request('var_ajaxcharset')&&_request('id_rubrique')) ajax_retour(afficher_contenu_rubrique(_request('id_rubrique'),$flag_trad,2));

 	pipeline('exec_init',array('args'=>array('exec'=>'articles_tous'),'data'=>''));
	debut_page(_T('titre_page_articles_tous'), "accueil", "tout-site");
	debut_gauche();
	echo formulaire_affiche_tous($aff_art, $aff_statut, $sel_lang);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_tous'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_tous'),'data'=>''));
	debut_droite();

	if ($enfant AND $browser_layer)
		echo couche_formulaire_tous($first_couche, $last_couche);

	$flag_trad = (($GLOBALS['meta']['multi_rubriques'] == 'oui' 
		OR $GLOBALS['meta']['multi_articles'] == 'oui') 
		AND $GLOBALS['meta']['gerer_trad'] == 'oui');

	$secteur24=http_wrapper("secteur-24.gif");
	$rubrique24=http_wrapper("rubrique-24.gif");
	$article24=http_wrapper("article-24.gif");
	global $spip_lang_left,$couleur_claire,$couleur_foncee;
	echo "<style type='text/css'>\n";
	echo <<<EOF
	
ul#myTree,ul#myTree ul {
	list-style: none;
}
ul#myTree ul {
	margin-left: 10px;
	padding-left: 12px;
	border-left: 1px dotted #888;
}
ul#myTree li {clear:both;}
ul#myTree li.sec,ul#myTree li.rub {display:inline;}
ul#myTree li.sec a, ul#myTree li.rub a {
	display:block;
	font-weight:bold;
	margin:0px;
	padding: 0px; 
	padding-top: 5px;
	padding-left: 5px; 
	margin-bottom:5px;
	height:24px;
}
ul#myTree li.art {display:block; height:24px;}
ul#myTree li.sec a {background-color: $couleur_claire;}
ul#myTree li.rub a {background-color: transparent;}
ul#myTree li.art a {
	display:inline;
	background-color: transparent;
	font-weight:normal;
	padding-left: 5px; 
	padding-top: 5px;
	height:24px;
}
span.icone {
	float:$spip_lang_left;
	position:relative;
	display:block;
	width:28px;
	height:24px;
	margin-left:-14px;	
}
ul#myTree li.art span.icone {	margin-left:0px;}

span.holder {}
li.sec span.icone {	background: url($secteur24) $spip_lang_left bottom no-repeat;}
li.rub span.icone {	background: url($rubrique24) $spip_lang_left top no-repeat;}
li.art span.icone {	background: url($article24) $spip_lang_left top no-repeat;}
li.sec ul{display:none;}
li.rub ul{display:none;}

ul#myTree li.art .puce_statut{float:$spip_lang_left;	padding-top: 5px;}
ul#myTree .expandImage{
	display:block;
	float:left;
	position:relative;
	left:-20px;
	width:16px;
	height:16px;
}
.selected {background-color: $couleur_claire;}
EOF;
	echo "</style>";
	 
	echo afficher_rubriques_filles(0, $flag_trad,2);


	echo fin_page();
}

// Voir inc_layer pour les 2 globales utilisees

// http://doc.spip.org/@arbo_articles_tous
function arbo_articles_tous()
{
	global $numero_block, $compteur_block;

	$enfant = array();
	$result = spip_query("SELECT id_rubrique, titre, id_parent FROM spip_rubriques ORDER BY 0+titre,titre");
	$first_couche = 0;
	while ($row = spip_fetch_array($result)) {
		$id_rubrique = $row['id_rubrique'];
		$id_parent = $row['id_parent'];
		$enfant[$id_parent][$id_rubrique] = typo($row['titre']);
		$nom_block = "rubrique$id_rubrique";
		if (!isset($numero_block[$nom_block])){
			$compteur_block++;
			$numero_block[$nom_block] = $compteur_block;

			if (!$first_couche) $first_couche = $compteur_block;
		}
	}
	$last_couche = $first_couche ? $compteur_block : 0;
	return array($enfant, $first_couche, $last_couche);
}


//  checkbox avec image

// http://doc.spip.org/@http_label_img
function http_label_img($statut, $etat, $var, $img, $texte) {
	return "<label for='$statut'>". 
		boutonne('checkbox',
			$var . '[]',
			$statut,
			(($etat !== false) ? ' checked="checked"' : '') .
			"id='$statut'") .
		"&nbsp;" .
		http_img_pack($img, $texte, "width='8' height='9' border='0'", $texte) .
		" " .
		$texte .
		"</label><br />";
}

// http://doc.spip.org/@formulaire_affiche_tous
function formulaire_affiche_tous($aff_art, $aff_statut,$sel_lang)
{
	$out = "";
	global $spip_lang_right;
	$out .= generer_url_post_ecrire("articles_tous"). 
		"<input type='hidden' name='aff_art[]' value='x'>";
	
	$out .= debut_boite_info(true);
	
	$out .= "<b>"._T('titre_cadre_afficher_article')."&nbsp;:</b><br />";
	
	if ($aff_statut['prepa'])
		$out .= http_label_img('prepa',
				    in_array('prepa', $aff_art),
				    'aff_art',
				    'puce-blanche-breve.gif',
				    _T('texte_statut_en_cours_redaction'));
	
	if ($aff_statut['prop'])
		$out .= http_label_img('prop',
				    in_array('prop', $aff_art),
				    'aff_art',
				    'puce-orange-breve.gif',
				    _T('texte_statut_attente_validation'));
		
	if ($aff_statut['publie'])
		$out .= http_label_img('publie',
				    in_array('publie', $aff_art),
				    'aff_art',
				    'puce-verte-breve.gif',
				    _T('texte_statut_publies'));
	
	if ($aff_statut['refuse'])
		$out .= http_label_img('refuse',
				    in_array('refuse', $aff_art),
				    'aff_art',
				    'puce-rouge-breve.gif',
				    _T('texte_statut_refuses'));
	
	if ($aff_statut['poubelle'])
		$out .= http_label_img('poubelle',
				    in_array('poubelle', $aff_art),
				    'aff_art',
				    'puce-poubelle-breve.gif',
				    _T('texte_statut_poubelle'));
	
	$out .= "\n<div align='$spip_lang_right'><INPUT TYPE='submit' CLASS='fondo' VALUE='"._T('bouton_changer')."'></div>";
	
	
	// GERER LE MULTILINGUISME
	if (($GLOBALS['meta']['multi_rubriques'] == 'oui' OR $GLOBALS['meta']['multi_articles'] == 'oui') AND $GLOBALS['meta']['gerer_trad'] == 'oui') {

		// bloc legende
		$lf = $GLOBALS['meta']['langue_site'];
		$out .= "<hr />\n<div class='verdana2'>";
		$out .= _T('info_tout_site6');
		$out .= "\n<div><span class='lang_base'>$lf</span> ". _T('info_tout_site5') ." </div>";
		$out .= "\n<div><span class='creer'>$lf</span> ". _T('info_tout_site2') ." </div>";
		$out .= "\n<div><a class='claire'>$lf</a> ". _T('info_tout_site3'). " </div>";
		$out .= "\n<div><a class='foncee'>$lf</a> ". _T('info_tout_site4'). " </div>";
		$out .= "</div>\n";
	
		// bloc choix de langue
		$langues = explode(',', $GLOBALS['meta']['langues_multilingue']);
		if (count($langues) > 1) {
			sort($langues);
			$out .= "<br />\n<div class='verdana2'><b>"._T('titre_cadre_afficher_traductions')."</b><br />";
			$out .= "<select style='width:100%' NAME='sel_lang[]' size='".count($langues)."' multiple='multiple'>";
			while (list(, $l) = each ($langues)) {
			  $out .= "<option value='$l'" .
			    (in_array($l,$sel_lang) ? " selected='selected'" : "") .
			    ">" .
			    traduire_nom_langue($l) .
			    "</option>\n"; 
			}
			$out .= "</select></div>\n";
	
			$out .= "\n<div align='$spip_lang_right'><INPUT TYPE='submit' NAME='Changer' CLASS='fondo' VALUE='"._T('bouton_changer')."'></div>";
		}
	
	}

	$out .= fin_boite_info(true);
	$out .= "</form>";
	
	$out .= debut_boite_info(true);
	$out .= _L("D&eacute;placements");
	$action = generer_action_auteur("reorganiser","",generer_url_ecrire('articles_tous'));
	$out .= "<form action='$action' method='post'>";
	$out .= form_hidden($action);
	$out .= "<textarea id='deplacements' style='display:none;' name='deplacements'></textarea>";
	$out .= "\n<div id='apply' style='display:none;text-align:$spip_lang_right'><input type='submit' class='fondo' value='"._T('bouton_changer')."'></div>";
	$out .= "</form>";
	$out .= fin_boite_info(true);
	return $out;
}

// http://doc.spip.org/@couche_formulaire_tous
function couche_formulaire_tous($first_couche, $last_couche)
{
	global $spip_lang_rtl;
	$out = "";
	
	$out .= "<div>&nbsp;</div>";
	$out .= "<b class='verdana3'>";
	$out .= "<a href=\"javascript:deplie_arbre()\">";
	$out .= _T('lien_tout_deplier');
	$out .= "</a>";
	$out .= "</b>";
	$out .= " | ";
	$out .= "<b class='verdana3'>";
	$out .= "<a href=\"javascript:plie_arbre()\">";
	$out .= _T('lien_tout_replier');
	$out .= "</a>";
	$out .= "</b>";
	$out .= "<div>&nbsp;</div>";
	return $out;
}

global $spip_lang_left, $spip_lang_right, $spip_lang, $couleur_claire;

function afficher_contenu_rubrique($id_rubrique,$flag_trad,$profondeur){
	static $ajax_args=NULL;
	global $enfant, $article;
	$out = "";
	if ($profondeur!=0){
		if (isset($article[$id_rubrique]))
			$out .= article_tous_rubrique($article[$id_rubrique], $id_rubrique, $flag_trad);
		if (isset($enfant[$id_rubrique]))
			$out .= afficher_rubriques_filles($id_rubrique,$flag_trad, $profondeur);
	}
	else{
		if ($ajax_args==NULL){
			$ajax_args = "";
			if (is_array($aff_art = _request('aff_art')))
				foreach($aff_art as $aff)
					$ajax_args.="&aff_art[]=$aff";
			if (is_array($sel_lang = _request('sel_lang')))
				foreach($sel_lang as $sel)
					$ajax_args.="&sel_lang[]=$sel";
		}
		$out = "<a href='".generer_url_ecrire('articles_tous',"id_rubrique=$id_rubrique&$ajax_args")."' class='ajax' rel='ul$id_rubrique'>"._T('info_tout_site')."</a>";
	}
	return $out;
}
// http://doc.spip.org/@afficher_rubriques_filles
function afficher_rubriques_filles($id_parent, $flag_trad, $profondeur=-1) {
	global $enfant, $article;
	$out = "";

	if (!$enfant[$id_parent]) return;
	$profondeur--;

	if ($id_parent==0){
		$titre = "Racine";
		$out .= "<ul id='myTree'><li id='rubrique-0' class='treeItem racine verdana2'>" .
		//'<img src="'._DIR_IMG_PACK.'deplierbas.gif" class="expandImage" />',
		"<span class='holder icone'>&nbsp;</span>$titre" .
		"\n<ul class=''>\n";
	}
	while (list($id_rubrique, $titre) = each($enfant[$id_parent]) ) {
		$out .= "<li id='rubrique-$id_rubrique' class='treeItem " .
			(($id_parent==0)?"sec":"rub") .
			"'>" .
			//$lesenfants?'<img src="'._DIR_IMG_PACK.'deplierhaut.gif" class="expandImage" />':'' .
		  "<span class='holder icone'> </span><a href='" .
		   generer_url_ecrire("naviguer","id_rubrique=$id_rubrique") .
		   "'>$titre</a>";
		
		$lesenfants = afficher_contenu_rubrique($id_rubrique,$flag_trad,$profondeur);
		if ($lesenfants)
			$out .= "\n<ul id='ul$id_rubrique'>\n$lesenfants</ul>\n";
		$out .= "</li>\n";
	}
	if ($id_parent==0)
		$out .= "</ul></li></ul>\n";
	return $out;
}

// http://doc.spip.org/@article_tous_rubrique
function article_tous_rubrique($tous, $id_rubrique, $flag_trad) 
{
	global $text_article;

	$res = '';
	while(list(,$zarticle) = each($tous) ) {
		$attarticle = &$text_article[$zarticle];
		$zelang = $attarticle["lang"];
		unset ($attarticle["trad"][$zelang]);
		if ($attarticle["id_trad"] == 0
		OR $attarticle["id_trad"] == $zarticle) {
			$auteurs = trouve_auteurs_articles($zarticle);

			$res .= "\n<li id='article-$zarticle' class='treeItem art tr_liste'>";
			if (count($attarticle["trad"]) > 0) {
				ksort($attarticle["trad"]);
				$res .= "\n<span class='trad_float'>" 
				.  join('',$attarticle["trad"])
				.  "</span>";
			}
			$res .= "\n"
				. "<span class='icone'> </span>"
			  . "<span class='puce_statut'>".puce_statut_article($zarticle, $attarticle["statut"], $id_rubrique)."</span>"
			  . ($flag_trad ? "<span class='lang_base'>$zelang</span> " : '')
			  . "<span><a"
			  . ($auteurs ? (' title="' . htmlspecialchars($auteurs). '"') :'')
			  . "\nhref='"
			  . generer_url_ecrire("articles","id_article=$zarticle")
			  . "'>"
			  . ($flag_trad ? "<span class='lang_base'>$zelang</span> " : '')
			  . "<span>"
			  . $attarticle["titre"]
			  . "</span></a>"
			  . "</li>";
		}
	}

	return (!$res ? '' : $res);
}

// http://doc.spip.org/@trouve_auteurs_articles
function trouve_auteurs_articles($id_article)
{
	$result = spip_query("SELECT nom FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien WHERE auteurs.id_auteur=lien.id_auteur AND lien.id_article=$id_article ORDER BY auteurs.nom");
	$res = array();
	while ($row = spip_fetch_array($result))  $res[] = extraire_multi($row["nom"]);
	return join(", ", $res);
}
?>
