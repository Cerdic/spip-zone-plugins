<?php

/*
 *  Plugin Bouquinerie pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_formater_livre_dist($row) {
	global $dir_lang, $options, $spip_lang_right, $spip_display;
	static $pret = false;
	static $chercher_logo, $img_admin, $formater_auteur, $nb, $langue_defaut, $afficher_langue;


	if (!$pret) {
		/*$chercher_logo = ($spip_display != 1 AND $spip_display != 4 AND $GLOBALS['meta']['image_process'] != "non");
		if ($chercher_logo) 
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
		$formater_auteur = charger_fonction('formater_auteur', 'inc');
		$img_admin = http_img_pack("admin-12.gif", "", " width='12' height='12'", _T('titre_image_admin_article'));
		$nb = ($options == "avancees");
		if (($GLOBALS['meta']['multi_rubriques'] == 'oui' AND (!isset($GLOBALS['id_rubrique']))) OR $GLOBALS['meta']['multi_articles'] == 'oui') {
			$afficher_langue = true;
			$langue_defaut = !isset($GLOBALS['langue_rubrique'])
			  ? $GLOBALS['meta']['langue_site']
			  : $GLOBALS['langue_rubrique'];
		}*/
		$pret = true;
	}

	if ($chercher_logo) {
		/*if ($logo = $chercher_logo($id_article, 'id_article', 'on')) {
			list($fid, $dir, $nom, $format) = $logo;
			include_spip('inc/filtres_images');
			$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
		}*/
	} else $logo ='';

	$vals = array();

	if ($lang = $row['lang']) changer_typo($lang);

	$id_livre = $row['id_livre'];
	$titre = $row['titre'];
	$descriptif = $row['descriptif'];
	$date = $row['date_ajout'];

	$vals[]= '<a href="'.generer_url_ecrire("livres_edit",'id_livre='.$row['id_livre']).'"'.
		' title="Modifier">'.
		'<img src="'.find_in_path('images/modifier.png').'"></a>';

	$vals[]= "<div>"
	. "<a href='"
	. generer_url_ecrire("livres","id_livre=$id_livre")
	. "'"
	. (!$descriptif ? '' : 
	     (' title="'.attribut_html(typo($descriptif)).'"'))
	. $dir_lang
	. " style=\"display:block;\">"
	. (!$logo ? '' :
	   ("<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>" . $logo . "</span>"))
	. (acces_restreint_rubrique($id_rubrique) ? $img_admin : '')
	. typo($titre)
	. (!($afficher_langue AND $lang != $GLOBALS['meta']['langue_site']) ? '' :
	   (" <span class='spip_xx-small' style='color: #666666'$dir_lang>(".traduire_nom_langue($lang).")</span>"))
	. (!$row['petition'] ? '' : (" <span class='spip_xx-small' style='color: red'>"._T('lien_petitions')."</span>"))
	. "</a>"
	. "</div>";


/*	$result = auteurs_article($id_article);
	$les_auteurs = array();
	while ($r = spip_fetch_array($result)) {
		list($s, $mail, $nom, $w, $p) = $formater_auteur($r['id_auteur']);
		$les_auteurs[]= "$mail&nbsp;$nom";
	}
	$vals[] = join('<br />', $les_auteurs);*/

	$s = affdate_jourcourt($date);
	$vals[] = $s ? $s : '&nbsp;';

	if  ($nb) $vals[]= afficher_numero_edit($id_livre, 'id_livre', 'livre');

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



/*	$ligne = '';

	$ligne .= '<tr>';

	$ligne .= '<td>'.
		;

	$ligne .='<a href="'.generer_url_action("supprimer_livre",'id_livre='.$row['id_livre']).'"'.
		' title="Supprimer">'.
		'<img src="'.find_in_path('images/supprimer.png').'"></a>';

	$ligne .=	'</td>';

	foreach ($row as $value) {
		$ligne .= '<td>'. $value . '</td>';
	}
	$ligne .= '</tr>';

	return $ligne;*/
}
?>
