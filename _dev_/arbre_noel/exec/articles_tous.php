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

function gerer_deplacements($deplacements){
	$liste_dep = explode("\n",$deplacements);
	if (count($liste_dep)){
		foreach ($liste_dep as $dep){
			$mouvement=explode(":",$dep);
			$quoi=explode("-",$mouvement[0]);
			$cible=explode("-",$mouvement[1]);
			if (in_array($quoi[0],array('article','rubrique')) && $cible[0]=='rubrique'){
				$id_quoi=intval($quoi[1]);$id_cible=intval($cible[1]);
				if ($quoi[0]=='article')
					spip_query("UPDATE spip_articles SET id_rubrique=".spip_abstract_quote($id_cible)." WHERE id_article=".spip_abstract_quote($id_quoi));
				if ($quoi[0]=='rubrique')
					spip_query("UPDATE spip_rubriques SET id_parent=".spip_abstract_quote($id_cible)." WHERE id_rubrique=".spip_abstract_quote($id_quoi));
			}
		}
		include_spip('inc/rubriques');
		propager_les_secteurs();
	}
}

// http://doc.spip.org/@exec_articles_tous_dist
function exec_articles_tous_dist()
{
	global $aff_art, $sel_lang, $article, $enfant, $text_article,$connect_toutes_rubriques;
	global $connect_id_auteur, $connect_statut, $spip_dir_lang, $spip_lang, $browser_layer;
	
	if (($connect_toutes_rubriques) && _request('deplacements')!==NULL)
		gerer_deplacements(_request('deplacements'));
	
	changer_typo(); // pour definir $dir_lang
	if (!is_array($aff_art)) $aff_art = array('prop','publie');

 	pipeline('exec_init',array('args'=>array('exec'=>'articles_tous'),'data'=>''));
	list($enfant, $first_couche, $last_couche) = arbo_articles_tous();
	debut_page(_T('titre_page_articles_tous'), "accueil", "tout-site");
	debut_gauche();
	
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

	formulaire_affiche_tous($aff_art, $aff_statut, $sel_lang);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_tous'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_tous'),'data'=>''));
	debut_droite();

	if ($enfant AND $browser_layer)
		couche_formulaire_tous($first_couche, $last_couche);

	$flag_trad = (($GLOBALS['meta']['multi_rubriques'] == 'oui' 
		OR $GLOBALS['meta']['multi_articles'] == 'oui') 
		AND $GLOBALS['meta']['gerer_trad'] == 'oui');

	$secteur24=http_wrapper("secteur-24.gif");
	$rubrique24=http_wrapper("rubrique-24.gif");
	$article24=http_wrapper("article-24.gif");
	global $spip_lang_left,$couleur_claire,$couleur_foncee;
	echo "<style type='text/css'>\n";
	echo <<<EOF
ul#myTree li {clear:both;}
ul#myTree li.secteur {
	padding-top: 5px; 
	padding-bottom: 5px; 
	background-color: $couleur_claire;
}
ul#myTree li.secteur ul{	background-color: white;}
ul#myTree li span.icone {
	display:block;
	float:$spip_lang_left;
	width:28px;
	height:24px;
}
li.secteur span.icone {	background: url($secteur24) $spip_lang_left bottom no-repeat;}
li.secteur ul{display:none;}
ul#myTree li.rubrique {	background-color: white;}
li.rubrique ul{display:none;}
li.rubrique span.icone {	background: url($rubrique24) $spip_lang_left top no-repeat;}
li.article span.icone {	background: url($article24) $spip_lang_left top no-repeat;}

.puce_statut{
float:$spip_lang_left;
}
ul#myTree, ul#myTree ul {
	list-style: none;
}
ul#myTree .expandImage{
	position:relative;
	left:-14px;
	float:left;
}
.selected { background-color:$couleur_foncee;border:2px solid $couleur_foncee;}
EOF;
	echo "</style>";
	 
	afficher_rubriques_filles(0, $flag_trad);


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
	global $spip_lang_right;
	echo generer_url_post_ecrire("articles_tous"), 
		"<input type='hidden' name='aff_art[]' value='x'>";
	
	debut_boite_info();
	
	 echo "<b>",_T('titre_cadre_afficher_article'),"&nbsp;:</b><br />";
	
	if ($aff_statut['prepa'])
		echo http_label_img('prepa',
				    in_array('prepa', $aff_art),
				    'aff_art',
				    'puce-blanche-breve.gif',
				    _T('texte_statut_en_cours_redaction'));
	
	if ($aff_statut['prop'])
		echo http_label_img('prop',
				    in_array('prop', $aff_art),
				    'aff_art',
				    'puce-orange-breve.gif',
				    _T('texte_statut_attente_validation'));
		
	if ($aff_statut['publie'])
		echo http_label_img('publie',
				    in_array('publie', $aff_art),
				    'aff_art',
				    'puce-verte-breve.gif',
				    _T('texte_statut_publies'));
	
	if ($aff_statut['refuse'])
		echo http_label_img('refuse',
				    in_array('refuse', $aff_art),
				    'aff_art',
				    'puce-rouge-breve.gif',
				    _T('texte_statut_refuses'));
	
	if ($aff_statut['poubelle'])
		echo http_label_img('poubelle',
				    in_array('poubelle', $aff_art),
				    'aff_art',
				    'puce-poubelle-breve.gif',
				    _T('texte_statut_poubelle'));
	
	echo "\n<div align='$spip_lang_right'><INPUT TYPE='submit' CLASS='fondo' VALUE='"._T('bouton_changer')."'></div>";
	
	
	// GERER LE MULTILINGUISME
	if (($GLOBALS['meta']['multi_rubriques'] == 'oui' OR $GLOBALS['meta']['multi_articles'] == 'oui') AND $GLOBALS['meta']['gerer_trad'] == 'oui') {

		// bloc legende
		$lf = $GLOBALS['meta']['langue_site'];
		echo "<hr />\n<div class='verdana2'>";
		echo _T('info_tout_site6');
		echo "\n<div><span class='lang_base'>$lf</span> ". _T('info_tout_site5') ." </div>";
		echo "\n<div><span class='creer'>$lf</span> ". _T('info_tout_site2') ." </div>";
		echo "\n<div><a class='claire'>$lf</a> ". _T('info_tout_site3'). " </div>";
		echo "\n<div><a class='foncee'>$lf</a> ". _T('info_tout_site4'). " </div>";
		echo "</div>\n";
	
		// bloc choix de langue
		$langues = explode(',', $GLOBALS['meta']['langues_multilingue']);
		if (count($langues) > 1) {
			sort($langues);
			echo "<br />\n<div class='verdana2'><b>"._T('titre_cadre_afficher_traductions')."</b><br />";
			echo "<select style='width:100%' NAME='sel_lang[]' size='".count($langues)."' multiple='multiple'>";
			while (list(, $l) = each ($langues)) {
			  echo "<option value='$l'",
			    (in_array($l,$sel_lang) ? " selected='selected'" : ""),
			    ">",
			    traduire_nom_langue($l),
			    "</option>\n"; 
			}
			echo "</select></div>\n";
	
			echo "\n<div align='$spip_lang_right'><INPUT TYPE='submit' NAME='Changer' CLASS='fondo' VALUE='"._T('bouton_changer')."'></div>";
		}
	
	}

	fin_boite_info();
	echo "</form>";
	
	debut_boite_info();
	echo _L("D&eacute;placements");
	echo generer_url_post_ecrire('articles_tous');
	echo "<textarea id='deplacements' style='display:none;' name='deplacements'></textarea>";
	echo "\n<div id='apply' style='display:none;text-align:$spip_lang_right'><input type='submit' class='fondo' value='"._T('bouton_changer')."'></div>";
	echo "</form>";
	fin_boite_info();

}

// http://doc.spip.org/@couche_formulaire_tous
function couche_formulaire_tous($first_couche, $last_couche)
{
	global $spip_lang_rtl;

	echo "<div>&nbsp;</div>";
	echo "<b class='verdana3'>";
	echo "<a href=\"javascript:deplie_arbre()\">";
	echo _T('lien_tout_deplier');
	echo "</a>";
	echo "</b>";
	echo " | ";
	echo "<b class='verdana3'>";
	echo "<a href=\"javascript:plie_arbre()\">";
	echo _T('lien_tout_replier');
	echo "</a>";
	echo "</b>";
	echo "<div>&nbsp;</div>";
}

global $spip_lang_left, $spip_lang_right, $spip_lang, $couleur_claire;

// http://doc.spip.org/@afficher_rubriques_filles
function afficher_rubriques_filles($id_parent, $flag_trad) {
	global $enfant, $article;
	static $decal = 0;

	if (!$enfant[$id_parent]) return;

	$decal = $decal + 1;

	if ($id_parent==0){
		$titre = "Racine";
		echo "<ul id='myTree'><li class='treeItem racine'>",
		"<span class='textHolder icone'>&nbsp;</span>$titre",
		"\n<ul class='plan-rubrique'>\n";
	}
	while (list($id_rubrique, $titre) = each($enfant[$id_parent]) ) {
			
		$lesarticles = isset($article[$id_rubrique]);
		$lesenfants = ($lesarticles OR isset($enfant[$id_rubrique]));

		echo "<li id='rubrique-$id_rubrique' class='treeItem ",
			($id_parent==0)?"secteur":"rubrique",
			"'>",
		  "<span class='textHolder icone'>&nbsp;</span>$titre";
		   
		if ($lesenfants) {
			echo "\n<ul class='plan-rubrique'>\n";
			if ($lesarticles) 
				echo article_tous_rubrique($article[$id_rubrique], $id_rubrique, $flag_trad);
			afficher_rubriques_filles($id_rubrique,$flag_trad);
			echo "</ul>\n";
		}
			
		echo "</li>\n";
	}
	if ($id_parent==0)
		echo "</ul></li></ul>\n";
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

			$res .= "\n<li id='article-$zarticle' class='treeItem article tr_liste'>";
			if (count($attarticle["trad"]) > 0) {
				ksort($attarticle["trad"]);
				$res .= "\n<span class='trad_float'>" 
				.  join('',$attarticle["trad"])
				.  "</span>";
			}
			$res .= "\n"
				. "<span class='icone'>&nbsp;</span>"
			  . "<span class='puce_statut'>".puce_statut_article($zarticle, $attarticle["statut"], $id_rubrique)."</span>"
			  . ($flag_trad ? "<span class='lang_base'>$zelang</span> " : '')
			  . "<span>"
			  . $attarticle["titre"]
			  . "</span>"
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
