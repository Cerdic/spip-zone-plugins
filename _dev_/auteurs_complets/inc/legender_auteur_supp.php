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

// http://doc.spip.org/@inc_legender_auteur_dist
function inc_legender_auteur_supp_dist($id_auteur, $auteur, $mode, $echec='', $redirect='')
{
	$corps_supp = (($mode < 0) OR !statut_modifiable_auteur_supp($id_auteur, $auteur))
	? legender_auteur_supp_voir($auteur, $redirect)
	: legender_auteur_supp_saisir($id_auteur, $auteur, $mode, $echec, $redirect);

	return  $redirect ? $corps_supp : ajax_action_greffe("legender_auteur_supp-$id_auteur", $corps_supp);
}

// La partie affichage du formulaire...
function legender_auteur_supp_saisir($id_auteur, $auteur, $mode, $echec='', $redirect=''){

	global $options, $connect_statut, $connect_id_auteur, $connect_toutes_rubriques;


	$setconnecte = ($connect_id_auteur == $id_auteur);

	$corps_supp = '';

// Le formulaire en lui meme...
	$corps_supp .= "<b>"._T('auteurscomplets:entree_nom_famille')."</b>"
	. "<br><input type='text' name='nom_famille' class='formo' value=\"".entites_html($auteur['nom_famille'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_prenom')."</b>"
	. "<br><input type='text' name='prenom' class='formo' value=\"".entites_html($auteur['prenom'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_organisation')."</b>"
	. "<br><input type='text' name='organisation' class='formo' value=\"".entites_html($auteur['organisation'])."\" size='40'>\n<p>\n"
	. "<b>"._T('auteurscomplets:entree_url_organisation')."</b>"
	. "<br><input type='text' name='url_organisation' class='formo' value=\"".entites_html($auteur['url_organisation'])."\" size='40'>\n<p>\n"
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

// Le bouton de validation...
	$corps_supp .= "<p />"
	. "\n<div align='right'>"
	. "\n<input type='submit' class='fondo' value='"
	. _T('bouton_enregistrer')
	. "'></div>";

	$arg = intval($id_auteur) . '/';

// Affichage du formulaire en Ajax qui reprend ce qu'il y a avant ...
	return '<div>&nbsp;</div>'
	. "\n<div class='serif'>"
	. debut_cadre_relief("fiche-perso-24.gif", true, "", _T("auteurscomplets:coordonnees_sup"))
	. ($redirect
	     ? generer_action_auteur('legender_auteur_supp', $arg, $redirect, $corps_supp)
	   : ajax_action_auteur('legender_auteur_supp', $arg, 'auteur_infos_supp', "id_auteur=$id_auteur&initial=-1&retour=$redirect", $corps_supp))
	. fin_cadre_relief(true)
	. '</div>';
}

// L'affichage des infos supplémentaires...
function legender_auteur_supp_voir($auteur, $redirect)
{
	global $connect_toutes_rubriques, $connect_statut, $connect_id_auteur, $spip_lang_right ;

// On récupère ce qui nous intéresse...
	$nom_famille=$auteur['nom_famille'];
	$prenom=$auteur['prenom'];
	$organisation=$auteur['organisation'];
	$url_organisation=$auteur['url_organisation'];
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

//Debut de l'affichage des données...
	$res = "<table width='100%' cellpadding='0' border='0' cellspacing='0'>"
	. "<tr>"
	. "<td valign='top' width='100%'>"
	. gros_titre(_T('auteurscomplets:coordonnees_sup'),'',false)
	. "<div>&nbsp;</div>";

// N'affichons que ce qui existe...
	if ($prenom || $nom) $res .= "<div>";
	if (strlen($prenom) > 2){ $res .= "$prenom";}
	if (strlen($nom_famille) > 2){ $res .= " $nom_famille";}
	if ($prenom || $nom) $res .= "</div>";
	if ($url_organisation) {
		if (!$organisation) $organisation = _T('auteurscomplets:affiche_organisation');
		$res .= propre(_T('auteurscomplets:affiche_organisation')." [{{".$organisation."}}->".$url_organisation."]");
	}
	if ((strlen($organisation) > 2) && !$url_organisation){ $res .= "<div>"._T('auteurscomplets:affiche_organisation')." $organisation </div>";}
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
	if (strlen($pays) > 2){ $res .= "<div>".propre($pays)."</div>";}
	$res .= "</td>"
	.  "<td>";

// Afficher le bouton d'affichage du formulaire...
	if (statut_modifiable_auteur_supp($id_auteur, $auteur)) {
		$ancre = "legender_auteur_supp-$id_auteur";
		$clic = _T("auteurscomplets:infos_supp");
		$h = generer_url_ecrire("auteur_infos_supp","id_auteur=$id_auteur&initial=0");
		if ((_SPIP_AJAX === 1 ) AND !$redirect) {
			$evt .= "\nonclick=" . ajax_action_declencheur($h,$ancre);
			 $h = "<a\nhref='$h#$ancre'$evt>$clic</a>";
		}
	  $res .= icone($clic, $h, "redacteurs-24.gif", "edit.gif", '', '',true);
	}

// Fermons tout ca...
	$res .= "</td></tr></table>";

//Allez on balance tout...
	return $res;
}

function statut_modifiable_auteur_supp($id_auteur, $auteur)
{
	global $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;

// on peut se changer soi-meme
	return  (($connect_id_auteur == $id_auteur) ||
// sinon on doit etre admin
// et pas admin restreint pour changer un autre admin ou creer qq
	(($connect_statut == "0minirezo") &&
	($connect_toutes_rubriques OR 
	($id_auteur AND ($auteur['statut'] != "0minirezo")))));
}
?>