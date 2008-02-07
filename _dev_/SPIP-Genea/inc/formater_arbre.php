<?php

/*******************************************************************
 *
 * Copyright (c) 2008
 * Xavier BUROT
 * fichier : inc/formater_arbre
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL
 *
 * *******************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction appelee dans une boucle, calculer les invariants au premier appel.

function inc_formater_arbre_dist($row){
	global $dir_lang, $options, $spip_lang_right, $spip_display;
	static $pret = false;
	static $chercher_logo, $img_admin, $formater_auteur, $nb, $langue_defaut, $afficher_langue;

	if (!$pret) {
		$chercher_logo = ($spip_display != 1 AND $spip_display != 4 AND $GLOBALS['meta']['image_process'] != "non");
		if ($chercher_logo)
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
//		$formater_auteur = charger_fonction('formater_auteur', 'inc');
		$img_admin = http_img_pack("admin-12.gif", "", " width='12' height='12'", _T('titre_image_admin_article'));
		$nb = ($options == "avancees");
		if (($GLOBALS['meta']['multi_rubriques'] == 'oui' AND (!isset($GLOBALS['id_rubrique']))) OR $GLOBALS['meta']['multi_articles'] == 'oui') {
			$afficher_langue = true;
			$langue_defaut = !isset($GLOBALS['langue_rubrique'])
			  ? $GLOBALS['meta']['langue_site']
			  : $GLOBALS['langue_rubrique'];
		}
		$pret = true;
	}

	$id_genea = $row['id_genea'];
	$titre = sinon($row['titre'], _T('ecrire:info_sans_titre'));
	$id_rubrique = $row['id_rubrique'];
//	$date = $row['date'];
//	$statut = $row['statut'];
//	$descriptif = $row['descriptif'];
//	if ($lang = $row['lang']) changer_typo($lang);
	$total_individus=$row["nombre"];

	if ($chercher_logo) {
		if ($logo = $chercher_logo($id_rubrique, 'id_rubrique', 'on')) {
			list($fid, $dir, $nom, $format) = $logo;
			include_spip('inc/filtres_images');
			$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
		}
	} else $logo ='';

	$vals = array();

	if ($id_rubrique>0) {
		if ($total_individus>0) {
			if (acces_restreint_rubrique($id_rubrique))
				$puce = 'puce-verte-anim.gif';
			else
				$puce='puce-verte-breve.gif';
			$statut = _T('genea:en_ligne_no_vide');
		}else{
			if (acces_restreint_rubrique($id_rubrique))
				$puce = 'puce-orange-anim.gif';
			else
				$puce='puce-orange-breve.gif';
			$statut = _T('genea:en_ligne_vide');
		}
		$title = _T('info_arbre_en_ligne');
	} else {
		if ($total_individus>0) {
			if (acces_restreint_rubrique($id_rubrique))
			$puce = 'puce-blanche-anim.gif';
			else
				$puce='puce-blanche-breve.gif';
			$statut = _T('genea:attente_no_vide');
		}else{
			if (acces_restreint_rubrique($id_rubrique))
				$puce = 'puce-rouge-anim.gif';
			else
				$puce='puce-rouge-breve.gif';
			$statut = _T('genea:attente_vide');
		}
		$title = _T('info_arbre_en_attente');
	}

	$vals[] = http_img_pack($puce, $statut, "class='puce'") ."&nbsp;&nbsp;";

	$vals[]= "<div>"
	. "<a href='"
	. generer_url_ecrire("genea_naviguer","action=voir&id_genea=$id_genea")
	. "'"
	. $dir_lang
	. " style=\"display:block;\">"
	. (!$logo ? '' :
	   ("<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>" . $logo . "</span>"))
	. (acces_restreint_rubrique($id_rubrique) ? $img_admin : '')
	. typo($titre)
	. (!($afficher_langue AND $lang != $GLOBALS['meta']['langue_site']) ? '' :
	   (" <span class='spip_xx-small' style='color: #666666'$dir_lang>(".traduire_nom_langue($lang).")</span>"))
	. "</a>"
	. "</div>";

	$vals[] = ($id_rubrique>0) ? "</a> &nbsp;&nbsp; <span class='spip_xx-small'><a href='" . generer_url_ecrire("naviguer","id_rubrique=$id_rubrique") . "'>"._T('rubrique')." N° $id_rubrique</a></span>" : "";

	$vals[] = $row['nombre'] ? "<span class='spip_xx-small' style='color: red'>$total_individus "._T('genea:nbre_individu')."</span>" : "&nbsp;";

	if  ($nb) $vals[]= afficher_numero_edit($id_genea, 'id_genea', 'genea');

	if ($options == "avancees") { // Afficher le numero (JMB)
		  $largeurs = array(11, '', 80, 100, 50);
		  $styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');
	} else {
		  $largeurs = array(11, '', 100, 100);
		  $styles = array('', 'arial2', 'arial1', 'arial1');
	}

	return ($spip_display != 4)
	? afficher_liste_display_neq4($largeurs, $vals, $styles)
	: afficher_liste_display_eq4($largeurs, $vals, $styles);
}

?>
