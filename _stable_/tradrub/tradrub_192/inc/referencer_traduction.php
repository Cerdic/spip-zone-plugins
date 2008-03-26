<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

// http://doc.spip.org/@inc_referencer_traduction_dist
function inc_referencer_traduction_dist($id, $flag, $id_rubrique, $id_trad, $trad_err='',$type='article')
{
	global $connect_statut, $couleur_claire, $options, $connect_toutes_rubriques, $spip_lang_right, $spip_display, $dir_lang;

	if (! (($GLOBALS['meta']['multi_articles'] == 'oui')
		OR (($GLOBALS['meta']['multi_rubriques'] == 'oui')
			AND ($GLOBALS['meta']['gerer_trad'] == 'oui'))) )
		return '';

	$langue_obj = spip_fetch_array(spip_query("SELECT lang FROM spip_".$type."s WHERE id_".$type."=$id"));

	$langue_obj = $langue_obj['lang'];

	$reponse = '';
	// Choix langue article
	if ($GLOBALS['meta']['multi_'.$type.'s'] == 'oui' AND $flag) {

		$row = spip_fetch_array(spip_query("SELECT lang FROM spip_rubriques WHERE id_rubrique=$id_rubrique"));
		$langue_parent = $row['lang'];

		if (!$langue_parent)
			$langue_parent = $GLOBALS['meta']['langue_site'];
		if (!$langue_obj)
			$langue_obj = $langue_parent;

		if ($menu = menu_langues('changer_lang', $langue_obj, _T('info_multi_cet_'.$type).' ', $langue_parent, 'ajax')) {
			$menu = ajax_action_post('referencer_traduction', "$id,$id_rubrique",$type."s","id_".$type."=$id&type=".$type, $menu, _T('bouton_changer'), " class='visible_au_chargement fondo'");

			$reponse .= debut_cadre_couleur('',true)
			. "\n<div style='text-align: center;'>"
			. $menu
			. "</div>\n"
			. fin_cadre_couleur(true);
		}
	}

	if ($trad_err)
		$reponse .= "<div><span style='color: red' size='2' face='verdana,arial,helvetica,sans-serif'>"._T('trad_deja_traduit'). "</span></div>";

		// Afficher la liste des traductions
		$f=$type.'s_traduction';
	$table = !$id_trad ? array() : $f($id, $id_trad);

		// bloc traductions
	if (count($table) > 0) {

		$largeurs = array(7, 12, '', 100);
		$styles = array('', '', 'arial2', 'arial2');

		$t = afficher_liste($largeurs, $table, $styles);
		if ($spip_display != 4)
		  $t = "\n<table width='100%' cellpadding='2' cellspacing='0' border='0'>"
		    . $t
		    . "</table>\n";

		$liste = "\n<div class='liste'>"
		. bandeau_titre_boite2( '<b>' . _T('trad_'.$type.'_traduction') . '</b>','', 'white', 'black', false)
		. $t
		. "</div>";
	} else $liste = '';

	// changer les globales $dir_lang etc
	changer_typo($langue_obj);

	// Participation aux Traductions pas pour Mal-voyant. A completer
	if ($spip_display == 4) $form =''; else {
	$form = "<table width='100%'><tr>";

	if ($flag AND $options == "avancees" AND !$table) {
			// Formulaire pour lier a un article
		$form .= "<td style='width: 60%' class='arial2'>"
		. ajax_action_post("referencer_traduction",
				$id,
				$type.'s',
				"id_".$type."=$id&type=$type",
				(_T('trad_lier') .
				 "\n<input type='text' class='fondl' name='lier_trad' size='5' />\n".
				 "\n<input type='hidden' name='type' value='".$type."' />\n"),
				_T('bouton_valider'),
				" class='fondl'")
		. "</td>\n"
		. "<td style='width: 10px'> &nbsp; </td>"
		. "<td style='width: 2px; background: url(" . _DIR_IMG_PACK . "tirets-separation.gif)'>". http_img_pack('rien.gif', " ", "width='2' height='2'") . "</td>"
		. "<td style='width: 10px'> &nbsp; </td>";
	}

	$form .= "<td>"
	. icone_horizontale(_T('trad_new'), generer_url_ecrire($type."s_edit","new=oui&lier_trad=$id&id_rubrique=$id_rubrique"), "traductions-24.gif", "creer.gif", false)
	. "</td>";

	if ($flag AND $options == "avancees" AND $table) {
		$clic = _T('trad_delier');
		$form .= "<td style='width: 10px'> &nbsp; </td>"
		. "<td style='width: 2px; background: url(" . _DIR_IMG_PACK . "tirets-separation.gif)'>". http_img_pack('rien.gif', " ", "width='2' height='2'") . "</td>"
		. "<td style='width: 10px'> &nbsp; </td>"
		. "<td>"
		  // la 1ere occurrence de clic ne sert pas en Ajax
		. icone_horizontale($clic, ajax_action_auteur("referencer_traduction","$id,-$id_trad,$type",$type.'s', "id_".$type."=$id&type=".$type,array($clic)), "traductions-24.gif", "supprimer.gif", false)
		. "</td>\n";
	}

	$form .= "</tr></table>";
	}
	if ($GLOBALS['meta']['gerer_trad'] == 'oui')
		$bouton = _T('titre_langue_trad_'.$type);
	else
		$bouton = _T('titre_langue_'.$type);

	if ($langue_obj)
		$bouton .= "&nbsp; (".traduire_nom_langue($langue_obj).")";

	if ($flag === 'ajax')
		$res = debut_cadre_enfonce('langues-24.gif', true, "",
				bouton_block_visible('languearticle,lier_traductions')
				. $bouton)
			. debut_block_visible('languearticle')
			. $reponse
			. fin_block()
			. $liste
			. debut_block_visible('lier_traductions')
			. $form
			. fin_block()
			. fin_cadre_enfonce(true);
	else $res =  debut_cadre_enfonce('langues-24.gif', true, "",
				bouton_block_invisible('languearticle,lier_traductions')
				. $bouton)
			. debut_block_invisible('languearticle')
			. $reponse
			. fin_block()
			. $liste
			. debut_block_invisible('lier_traductions')
			. $form
			. fin_block()
			. fin_cadre_enfonce(true);
	return ajax_action_greffe("referencer_traduction-$id", $res);
}


// http://doc.spip.org/@articles_traduction
function articles_traduction($id_article, $id_trad)
{
	global $connect_toutes_rubriques, $dir_lang;

	$result_trad = spip_query("SELECT id_article, id_rubrique, titre, lang, statut FROM spip_articles WHERE id_trad = $id_trad");

	$table= array();

	while ($row = spip_fetch_array($result_trad)) {
		$vals = array();
		$id_article_trad = $row["id_article"];
		$id_rubrique_trad = $row["id_rubrique"];
		$titre_trad = $row["titre"];
		$lang_trad = $row["lang"];
		$statut_trad = $row["statut"];

		changer_typo($lang_trad);
		$titre_trad = "<span $dir_lang>$titre_trad</span>";

		$vals[] = http_img_pack("puce-".puce_statut($statut_trad).'.gif', "", " class='puce'");

		if ($id_article_trad == $id_trad) {
			$vals[] = http_img_pack('langues-12.gif', "", " class='lang'");
			$titre_trad = "<b>$titre_trad</b>";
		} else {
		  if (!$connect_toutes_rubriques)
			$vals[] = http_img_pack('langues-off-12.gif', "", " class='lang'");
		  else
		    $vals[] = ajax_action_auteur("referencer_traduction", "$id_article,$id_trad,$id_article_trad", 'articles', "id_article=$id_article", array(http_img_pack('langues-off-12.gif', _T('trad_reference'), "class='lang'"), ' title="' . _T('trad_reference') . '"'));
		}

		$s = typo($titre_trad);
		if ($id_article_trad != $id_article)
			$s = "<a href='" . generer_url_ecrire("articles","id_article=$id_article_trad") . "'>$s</a>";
		if ($id_article_trad == $id_trad)
			$s .= " "._T('trad_reference');

		$vals[] = $s;
		$vals[] = traduire_nom_langue($lang_trad);
		$table[] = $vals;
	}

	return $table;
}
// http://doc.spip.org/@rubriques_traduction
function rubriques_traduction($id_rubrique, $id_trad)
{
	global $connect_toutes_rubriques, $dir_lang;

	$result_trad = spip_query("SELECT id_rubrique, id_rubrique, titre, lang, statut FROM spip_rubriques WHERE id_trad = $id_trad");

	$table= array();

	while ($row = spip_fetch_array($result_trad)) {
		$vals = array();
		$id_rubrique_trad = $row["id_rubrique"];
		$titre_trad = $row["titre"];
		$lang_trad = $row["lang"];
		$statut_trad = $row["statut"];

		changer_typo($lang_trad);
		$titre_trad = "<span $dir_lang>$titre_trad</span>";

		$vals[] = http_img_pack("puce-".puce_statut($statut_trad).'.gif', "", " class='puce'");

		if ($id_rubrique_trad == $id_trad) {
			$vals[] = http_img_pack('langues-12.gif', "", " class='lang'");
			$titre_trad = "<b>$titre_trad</b>";
		} else {
		  if (!$connect_toutes_rubriques)
			$vals[] = http_img_pack('langues-off-12.gif', "", " class='lang'");
		  else
		    $vals[] = ajax_action_auteur("referencer_traduction", "$id_rubrique,$id_trad,$id_rubrique_trad,rubrique", 'rubriques', "id_rubrique=$id_rubrique&type=rubrique", array(http_img_pack('langues-off-12.gif', _T('trad_reference'), "class='lang'"), ' title="' . _T('trad_reference') . '"'));
		}

		$s = typo($titre_trad);
		if ($id_rubrique_trad != $id_rubrique)
			$s = "<a href='" . generer_url_ecrire("rubriques","id_rubrique=$id_rubrique_trad") . "'>$s</a>";
		if ($id_rubrique_trad == $id_trad)
			$s .= " "._T('trad_reference');

		$vals[] = $s;
		$vals[] = traduire_nom_langue($lang_trad);
		$table[] = $vals;
	}

	return $table;
}
?>
