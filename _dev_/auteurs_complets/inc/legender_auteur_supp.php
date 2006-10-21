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
include_spip('inc/auteurs_complets_gestion');

// http://doc.spip.org/@inc_legender_auteur_dist
function inc_legender_auteur_supp_dist($id_auteur, $auteur, $mode, $echec='', $redirect='')
{
	$corps_supp = (($mode < 0) OR !statut_modifiable_auteur($id_auteur, $auteur))
	? legender_auteur_supp_voir($auteur, $redirect)
	: legender_auteur_supp_saisir($id_auteur, $auteur, $mode, $echec, $redirect);
	
	return  $redirect ? $corps_supp :
	  ajax_action_greffe("legender_auteur_supp-$id_auteur", $corps_supp);

}

// http://doc.spip.org/@legender_auteur_saisir
function legender_auteur_supp_saisir($id_auteur, $auteur, $mode, $echec='', $redirect=''){
	

	global $options, $connect_statut, $connect_id_auteur, $connect_toutes_rubriques;

	$corps_supp = '';

	if ($echec){

		foreach (split('@@@',$echec) as $e)
			$corps_supp .= '<p>' . _T($e) . "</p>\n";
		
		$corps_supp = debut_cadre_relief('', true)
		.  http_img_pack("warning.gif", _T('info_avertissement'), "width='48' height='48' align='left'")
		.  "<div style='color: red; left-margin: 5px'>"
		. $corps_supp
		. "<p>"
		.  _T('info_recommencer')
		.  "</p></div>\n"
		. fin_cadre_relief(true)
		.  "\n<p>";
	}

	$corps_supp .= "<b>"._T('auteurscomplets:entree_organisation')."</b>"
	. "<br><input type='text' name='organisation' class='formo' value=\"".entites_html($auteur['organisation'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_telephone')."</b>"
	. "<br><input type='text' name='telephone' class='formo' value=\"".entites_html($auteur['telephone'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_fax')."</b>"
	. "<br><input type='text' name='fax' class='formo' value=\"".entites_html($auteur['fax'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_skype')."</b>"
	. "<br><input type='text' name='skype' class='formo' value=\"".entites_html($auteur['skype'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_adresse')."</b><BR>\n"
	. "<input type='text' name='adresse' class='formo' value=\"".entites_html($auteur['adresse'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_codepostal')."</b>"
	. "<br><input type='text' name='codepostal' class='formo' value=\"".entites_html($auteur['codepostal'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_ville')."</b>"
	. "<br><input type='text' name='ville' class='formo' value=\"".entites_html($auteur['ville'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_pays')."</b>"
	. "<br><input type='text' name='pays' class='formo' value=\"".entites_html($auteur['pays'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_latitude')."</b>"
	. "<br><input type='text' name='latitude' class='formo' value=\"".entites_html($auteur['latitude'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_longitude')."</b>"
	. "<br><input type='text' name='longitude' class='formo' value=\"".entites_html($auteur['longitude'])."\" size='40'>\n<p>\n";

	$corps_supp .= "<p />"
	. "\n<div align='right'>"
	. (!$setconnecte ? '' : apparait_auteur_infos($id_auteur, $auteur))
	. "\n<input type='submit' class='fondo' value='"
	. _T('bouton_enregistrer')
	. "'></div>";

	$arg = intval($id_auteur) . '/';

	return '<div>&nbsp;</div>'
	. "\n<div class='serif'>"
	. debut_cadre_relief("fiche-perso-24.gif", true, "", _T("auteurscomplets:coordonnees_sup"))
	. ($redirect
	     ? generer_action_auteur('legender_auteur_supp', $arg, $redirect, $corps_supp)
	   : ajax_action_auteur('legender_auteur_supp', $arg, 'auteur_infos_supp', "id_auteur=$id_auteur&initial=-1&retour=$redirect", $corps_supp))
	. fin_cadre_relief(true)
	. '</div>';

}

// http://doc.spip.org/@legender_auteur_voir
function legender_auteur_supp_voir($auteur, $redirect)
{
	global $connect_toutes_rubriques, $connect_statut, $connect_id_auteur, $options,$spip_lang_right ;

	$organisation=$auteur['organisation'];
	$telephone=$auteur['telephone'];
	$fax=$auteur['fax'];
	$adresse=$auteur['adresse'];
	$codepostal=$auteur['codepostal'];
	$ville=$auteur['ville'];
	$pays=$auteur['pays'];
	$latitude=$auteur['latitude'];
	$longitude=$auteur["longitude"];
	$skype = $auteur["skype"];
	$id_auteur=$auteur['id_auteur'];

	$res .= "<table width='100%' cellpadding='0' border='0' cellspacing='0'>"
	. "<tr>"
	. "<td valign='top' width='100%'>"
	. gros_titre(_T('auteurscomplets:coordonnees_sup'),'',false)
	. "<div>&nbsp;</div>";

	if (strlen($organisation) > 2){ $res .= "<div>"._T('auteurscomplets:affiche_organisation')." $organisation </div>";}
	if (strlen($telephone) > 2){ $res .= "<div>"._T('auteurscomplets:affiche_telephone')." $telephone </div>";}
	if (strlen($fax) > 2){ $res .= "<div>"._T('auteurscomplets:affiche_fax')." $fax </div>";}
	if (strlen($skype) > 2){ $res .= "<div>"._T('auteurscomplets:affiche_skype')." $skype </div><hr />";}
	if ((strlen($latitude) > 2) || (strlen($longitude) >2)){  $res .= "<div><b>"._T('auteurscomplets:affiche_coordonnees_geo')."</b></div>";}
	if (strlen($latitude) > 2){ $res .= "<div>"._T('auteurscomplets:affiche_latitude')." $latitude </div>";}
	if (strlen($longitude) > 2){ $res .= "<div>"._T('auteurscomplets:affiche_longitude')." $longitude </div><hr />";}
	if ((strlen($adresse) > 2) || (strlen($codepostal) >2) || (strlen($ville) > 2) || (strlen($pays) > 2)){ $res .= "<div><b>"._T('auteurscomplets:affiche_adresse')."</b></div>";}
	if (strlen($adresse) > 2){ $res .= "<div> $adresse </div>";}
	if (strlen($codepostal) > 2){ $res .= "<div> $codepostal ";}
	if (strlen($ville) > 2){ $res .= "$ville </div>";}
	if (strlen($pays) > 2){ $res .= "<div> $pays </div>";}
	$res .= "</td>"
	.  "<td>";

	if (statut_modifiable_auteur($id_auteur, $auteur)) {
		$ancre = "legender_auteur_supp-$id_auteur";
		$clic = _T("admin_modifier_auteur_supp");
		$h = generer_url_ecrire("auteur_infos_supp","id_auteur=$id_auteur&initial=0");
		if (($_COOKIE['spip_accepte_ajax'] == 1 ) AND !$redirect) {
		  $evt .= "\nonclick=" . ajax_action_declencheur($h,$ancre);
		  $h = "<a\nhref='$h$a'$evt>$clic</a>";
		}
	  $res .= icone($clic, $h, "redacteurs-24.gif", "edit.gif", '', '',true);
	}

	$res .= "</td></tr></table>";

	return $res;
}
?>