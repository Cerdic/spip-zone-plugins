<?php

if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) return;

// ces fonctions seront redondantes avec la prochaine API de spip
// TODO spip 2.0 : supprimer ce fichier pour utiliser l'API objet de spip


/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_TRANCHES', 10);

include_spip("inc/presentation");

// Cette fonction prend un argument un tableau decrivant une requete Select
// et une fonction formatant chaque ligne du resultat de la requete
// Elle renvoie une enumeration HTML de ces lignes formatees, 
// avec une pagination appelable en Ajax si $idom et $url sont fournis

// http://doc.spip.org/@inc_presenter_liste_dist
function inc_bouq_presenter_liste_dist($requete, $fonc, &$prims, $own, $force, $styles, $idom='', $title='', $icone='', $url='', $cpt=NULL)
{
	global $spip_display, $spip_lang_left;

	// $requete est passe par reference, pour modifier l'index LIMIT
	if ($idom AND $spip_display != 4)
		$tranches = bouq_affiche_tranche_bandeau($requete, $idom, $url, $cpt, _TRANCHES);
	else $tranches = '';

	$prim = $prims;
	$prims = array();
	$result = sql_select((isset($requete["SELECT"]) ? $requete["SELECT"] : "*"), $requete['FROM'], $requete['WHERE'], $requete['GROUP BY'], $requete['ORDER BY'], $requete['LIMIT']);

	if (!sql_count($result)) {
		if (!$force) return '';
	} else {
	if ($spip_display != 4) {
		$evt = !preg_match(",msie,i", $GLOBALS['browser_name']) ? ''
		: "
			onmouseover=\"changeclass(this,'tr_liste_over');\"
			onmouseout=\"changeclass(this,'tr_liste');\"" ;

		$table = $head = '';
		$th = 0;
		while ($r = sql_fetch($result)) {
		  if ($prim) $prims[]= $r[$prim];
		  if ($vals = $fonc($r, $own)) {
			reset($styles);
			$res = '';
			foreach ($vals as $t) {
				list(,list($style, $largeur, $nom)) = each($styles);
				if ($largeur) $largeur = " style='width: $largeur" ."px;'";
				if ($style) $style = " class=\"$style\"";
				$t = !trim($t) ? "&nbsp;" : lignes_longues($t);
				$res .= "\n<td$style>$t</td>";
				if (!$table) {
				  $th |= $nom ? 1 : 0;
				  $head .= "\n<th style='text-align:center'>$nom</th>";
				}
			}
			$table .= "\n<tr class='tr_liste'$evt>$res</tr>";
		  }
		}
		if (!$th) $head= '';
		$tranches .= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>$head$table</table>";
	} else {
		while ($r = sql_fetch($result)) {
			if ($prim) $prims[]= $r[$prim];
			if ($t = $fonc($r, $own)) {
			  	$tranches = '<li>' . join('</li><li>', $t) . '</li>';
		$tranches = "\n<ul style='text-align: $spip_lang_left; background-color: white;'>"
		. $tranches
		. "</ul>";
			}
		}
	}
	sql_free($result);
	}

	$id = 't'.substr(md5(join('',$requete)),0,8);
	$bouton = !$icone ? '' : bouton_block_depliable($title, true, $id);

	return debut_cadre('liste', $icone, "", $bouton, "", "", false)
	  . debut_block_depliable(true,  $id)
	  . $tranches
	  . fin_block()
	  . fin_cadre('liste');
}

// http://doc.spip.org/@afficher_tranches_requete
function bouq_afficher_tranches_requete($num_rows, $idom, $url='', $nb_aff = 10, $old_arg=NULL) {
	static $ancre = 0;
	global $browser_name, $spip_lang_right, $spip_display;
	if ($old_arg!==NULL){ // eviter de casser la compat des vieux appels $cols_span ayant disparu ...
		$idom = $url;		$url = $nb_aff; $nb_aff=$old_arg;
	}

	$ancre++;
	$self = self();
	$ie_style = ($browser_name == "MSIE") ? "height:1%" : '';
	$style = "style='visibility: hidden; float: $spip_lang_right'";
	$nav= navigation_pagination($num_rows, $nb_aff, $url, _request($idom), $idom, true);
	$script = parametre_url($self, $idom, -1);
	$l = htmlentities(_T('lien_tout_afficher'));

	return http_img_pack("searching.gif", "*", "$style id='img_$idom'")
	  . "\n<div style='$ie_style;' class='arial1 tranches' id='a$ancre'>"
	  . $nav
	  . "&nbsp;&nbsp;&nbsp;<a href='$script#a$ancre' class='plus'"
	  . (!$url ? '' : generer_onclic_ajax($url, $idom,-1))
	  . "><img title=\"$l\" alt=\"$l\"\nsrc=\""
	  . find_in_path('images/plus.gif')
	  . "\" /></a></div>\n";
}

// http://doc.spip.org/@affiche_tranche_bandeau
function bouq_affiche_tranche_bandeau(&$requete, $idom, $url='', $cpt=NULL, $pas=10)
{
	if (!isset($requete['GROUP BY'])) $requete['GROUP BY'] = '';

	if ($cpt === NULL)
		$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM " . $requete['FROM'] . ($requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '') . ($requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '')));

	$deb_aff = intval(_request($idom));
	$nb_aff = $pas + ($pas>>1);

	if (isset($requete['LIMIT'])) $cpt = min($requete['LIMIT'], $cpt);

	if ($cpt > $nb_aff) {
		$nb_aff = $pas;
		$res = bouq_afficher_tranches_requete($cpt, $idom, $url, $nb_aff);
	} else $res = '';

	if (!isset($requete['LIMIT']) AND $deb_aff <> -1)
		$requete['LIMIT'] = "$deb_aff, $nb_aff";

	return $res;
}


function navigation_pagination($num_rows, $nb_aff=10, $href=null, $debut, $tmp_var=null, $on='') {

	$texte = '';
	$self = parametre_url(self(), 'date', '');
	$deb_aff = intval($debut);

	$num_rows = $num_rows['n'];

	for ($i = 0; $i < $num_rows; $i += $nb_aff){
		$deb = $i + 1;
		
		// Pagination : si on est trop loin, on met des '...'
		if (abs($deb-$deb_aff)>101) {
			if ($deb<$deb_aff) {
				if (!isset($premiere)) {
					$premiere = '0 ... ';
					$texte .= $premiere;
				}
			} else {
				$derniere = ' | ... '.$num_rows;
				$texte .= $derniere;
				break;
			}
		} else {

			$fin = $i + $nb_aff;
			if ($fin > $num_rows)
				$fin = $num_rows;

			if ($deb > 1)
				$texte .= " |\n";
			if ($deb_aff + 1 >= $deb AND $deb_aff + 1 <= $fin) {
				$texte .= "<b>$deb</b>";
			}
			else {
				$script = parametre_url($self, $tmp_var, $deb-1);
				if ($on) $on = generer_onclic_ajax($href, $tmp_var, $deb-1);
				$texte .= "<a href=\"$script\"$on>$deb</a>";
			}
		}
	}

	return $texte;
}

function generer_onclic_ajax($url, $idom, $val)
	{
	return "\nonclick=\"return charger_id_url('"
	. parametre_url($url, $idom, $val)
	. "','"
	. $idom
	. '\');"';
}

function debut_block_depliable($deplie,$id=""){
	$class=' blocdeplie';
	// si on n'accepte pas js, ne pas fermer
	if (_SPIP_AJAX AND !$deplie)
		$class=" blocreplie";
	return "<div ".($id?"id='$id' ":"")."class='bloc_depliable$class'>";   
}

?>

