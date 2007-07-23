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

	if (lire_config('association/indexation')=="id_asso")
	{$id=$auteur['id_asso'];}
	else{$id=$auteur['id_adherent'];}
	$setconnecte = ($connect_id_auteur == $id_auteur);

	$corps_supp = '';

// Le formulaire en lui meme...
	$corps_supp .= "<b>"._T('asso:adherent_libelle_reference_interne')."</b>"
	. "<input type='text' name='id_asso' class='formo' readonly='true' value='".$id."' />\n"
	. "<b>"._T('asso:adherent_libelle_nom')."</b>"
	. "<input type='text' name='nom' class='formo' value=\"".entites_html($auteur['nom'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_prenom')."</b>"
	. "<input type='text' name='prenom' class='formo' value=\"".entites_html($auteur['prenom'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_sexe')."</b><br />";
	$corps_supp .= _T('asso:adherent_libelle_homme')."<input type='radio' name='sexe'  value='H'";
	if ($auteur['sexe']=='H') {$corps_supp.= "checked='checked'";}
	$corps_supp.= " />\n";
	$corps_supp .= _T('asso:adherent_libelle_femme')."<input type='radio' name='sexe'  value='F'";
	if ($auteur['sexe']=='F') {$corps_supp.= "checked='checked'";}
	$corps_supp.= " />\n";
	$corps_supp .= "<br /><b>"._T('asso:adherent_libelle_date_naissance')."</b>"
	. "<input type='text' name='naissance' class='formo' value=\"".entites_html($auteur['naissance'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_fonction')."</b>"
	. "<input type='text' name='fonction' class='formo' value=\"".entites_html($auteur['fonction'])."\" />\n"	
	. "<b>"._T('asso:adherent_libelle_rue')."</b>"
	. "<textarea name='rue' class='formo'>".entites_html($auteur['rue'])."</textarea>\n"
	. "<b>"._T('asso:adherent_libelle_codepostal')."</b>"
	. "<input type='text' name='cp' class='formo' value=\"".entites_html($auteur['cp'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_ville')."</b>"
	. "<input type='text' name='ville' class='formo' value=\"".entites_html($auteur['ville'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_portable')."</b>"
	. "<input type='text' name='portable' class='formo' value=\"".entites_html($auteur['portable'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_telephone')."</b>"
	. "<input type='text' name='telephone' class='formo' value=\"".entites_html($auteur['telephone'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_profession')."</b>"
	. "<br /><input type='text' name='profession' class='formo' value=\"".entites_html($auteur['profession'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_societe')."</b>"
	. "<input type='text' name='societe' class='formo' value=\"".entites_html($auteur['societe'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_secteur')."</b>"
	. "<select type='text' name='secteur' class='formo' />\n";
	
	$arr=(lire_config('association/secteurs'));
	$arr=explode(",", $arr);
	foreach ($arr as $value){
	$corps_supp .= "<option value='".$value."'";
	if ($value==$auteur['secteur']) {$corps_supp.= "selected='selected'";}
	$corps_supp.= " />".$value."</option>\n";}
	$corps_supp.= "</select> \n";
	$corps_supp.= "<b>"._T('asso:adherent_libelle_accord')."</b><br />";
	$corps_supp .= _T('asso:adherent_libelle_oui')."<input type='radio' name='publication'  value='oui'";
	if ($auteur['publication']=='oui') {$corps_supp.= "checked='checked'";}
	$corps_supp.= " />\n";
	$corps_supp .= _T('asso:adherent_libelle_non')."<input type='radio' name='publication'  value='non'";
	if ($auteur['publication']=='non') {$corps_supp.= "checked='checked'";}
	$corps_supp.= " />\n"
	. "<br /><b>"._T('asso:adherent_libelle_utilisateur1')."</b>"
	. "<br /><input type='text' name='utilisateur1' class='formo' value=\"".entites_html($auteur['utilisateur1'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_utilisateur2')."</b><br />";	
	$corps_supp.= _T('asso:adherent_libelle_oui')."<input type='radio' name='utilisateur2'  value='oui'";
	if ($auteur['utilisateur2']=='oui') {$corps_supp.= "checked='checked'";}
	$corps_supp.= " />\n"
	. _T('asso:adherent_libelle_non')."<input type='radio' name='utilisateur2'  value='non'";
	if ($auteur['utilisateur2']=='non') {$corps_supp.= "checked='checked'";}
	$corps_supp.= " />\n"
	. "<br /><b>"._T('asso:adherent_libelle_utilisateur3')."</b>"
	. "<input type='text' name='utilisateur3' class='formo' value=\"".entites_html($auteur['utilisateur3'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_utilisateur4')."</b>"
	. "<input type='text' name='utilisateur4' class='formo' value=\"".entites_html($auteur['utilisateur4'])."\" />\n"
	. "<b>"._T('asso:adherent_libelle_categorie')."</b>"
	. "<select type='text' name='categorie' class='formo' />\n"
	. "<option value=''></option>";
	$query=spip_query( "SELECT * FROM spip_asso_categories ");
	while ($data=spip_fetch_array($query)) {
	$corps_supp .= "<option value='".$data['valeur']."'";
	if ($auteur['categorie']==$data['valeur']) {$corps_supp.= "selected='selected'";}
	$corps_supp .= "'>".$data['libelle']."</option>";
	}
	$corps_supp.= "</select>\n"
	. "<b>"._T('asso:adherent_libelle_remarques')."</b>"
	. "<textarea name='remarques' class='formo'>".entites_html($auteur['remarques'])."</textarea>\n";

	$att = " style='float:         "
	. $GLOBALS['spip_lang_right']
	. "' class='fondo'";
	$arg = intval($id_auteur) . '/';

// Affichage du formulaire en Ajax qui reprend ce qu'il y a avant ...
	return '<div>&nbsp;</div>'
	. "\n<div class='serif'>"
	. debut_cadre_relief("fiche-perso-24.gif", true, "", _T("asso:adherent_libelle_donnees_adherent"))
	. ($redirect
	     ? generer_action_auteur('legender_auteur_supp', $arg, $redirect, $corps_supp)
	   : ajax_action_post('legender_auteur_supp', $arg, 'auteur_infos_supp', "id_auteur=$id_auteur&initial=-1&retour=$redirect", $corps_supp, _T('bouton_enregistrer'), $att))
	. fin_cadre_relief(true)
	. '</div>';
}

// L'affichage des infos supplémentaires...
function legender_auteur_supp_voir($auteur, $redirect)
{
	global $connect_toutes_rubriques, $connect_statut, $connect_id_auteur, $spip_lang_right ;

// On récupère ce qui nous intéresse...
	$id_auteur=$auteur['id_auteur'];
	$id_asso=$auteur['id_asso'];
	$id_adherent=$auteur['id_adherent'];
	$nom=$auteur['nom'];
	$prenom=$auteur['prenom'];
	$naissance=$auteur['naissance'];
	$sexe=$auteur['sexe'];
	$email=$auteur['email'];
	$rue=$auteur['rue'];
	$numero=$auteur['numero'];
	$ville=$auteur['ville'];
	$cp=$auteur['cp'];
	$telephone=$auteur['telephone'];
	$portable=$auteur['portable'];
	$profession=$auteur['profession'];
	$societe=$auteur['societe'];
	$secteur=$auteur['secteur'];
	$categorie=$auteur['categorie'];
	$fonction=$auteur['fonction'];
	$publication=$auteur['publication'];
	$validite=$auteur['validite'];
	$utilisateur1=$auteur['utilisateur1'];
	$utilisateur2=$auteur['utilisateur2'];
	$utilisateur3=$auteur['utilisateur3'];
	$utilisateur4=$auteur['utilisateur4'];
	$remarques=$auteur['remarques'];	
	$statut=$auteur['statut'];

//Debut de l'affichage des données...
	$res = debut_cadre_relief("redacteurs-24.gif", true)
	. "<table width='100%' cellpadding='0' border='0' cellspacing='0'>"
	. "<tr>"
	. "<td valign='top' width='100%'>"
	. gros_titre(_T('asso:adherent_libelle_donnees_adherent'),'',false)
	. "<div>&nbsp;</div>";

// N'affichons que ce qui existe...
	$res .= "<div>";
	if ($prenom) {$res .= $prenom." " ;}
	$res .= $nom."</div>";	
	if ($fonction){ $res .= "<div>"._T('asso:adherent_libelle_fonction').": ".$fonction."</div>";}
	if ($id_asso){ $res .= "<div>"._T('asso:adherent_libelle_reference_interne_abrev').": ".$id_asso."</div>";}
	if ($categorie){ $res .= "<div>"._T('asso:adherent_libelle_categorie').": ".$categorie. "</div>";}
	if ($validite!='0000-00-00'){ $res .= "<div>"._T('asso:adherent_libelle_validite').": ".$validite. "</div>";}
	if ($statut){ $res .= "<div>"._T('asso:adherent_libelle_statut').": "._T('asso:adherent_libelle_statut_'.$statut)."</div>";}
	$res .= "<br />";
	if ($rue){ $res .= "<div>".$rue."</div>";}
	if ($cp || $ville){ 
		$res .= "<div>";
		if ($cp){ $res .= $cp." ";} if ($ville){ $res .= $ville;}
		$res .= "</div>";
	}
	if ($portable){ $res .= "<div>"._T('asso:adherent_libelle_portable').": ".$portable. "</div>";}
	if ($telephone){ $res .= "<div>"._T('asso:adherent_libelle_telephone').": ".$telephone."</div>";}
	$res .="<br />";
	if ($profession){ $res .= "<div>"._T('asso:adherent_libelle_profession').": ".$profession. "</div>";}
	if ($societe){ $res .= "<div>"._T('asso:adherent_libelle_societe').": ".$societe. "</div>";}
	if ($secteur){ $res .= "<div>"._T('asso:adherent_libelle_secteur').": ".$secteur. "</div>";}
	if ($publication){ $res .= "<div>"._T('asso:adherent_libelle_accord').": ".$publication. "</div>";}	
	if ($utilisateur1){ $res .= "<div>"._T('asso:adherent_libelle_utilisateur1').": ".$utilisateur1. "</div>";}
	if ($utilisateur2){ $res .= "<div>"._T('asso:adherent_libelle_utilisateur2').": ".$utilisateur2. "</div>";}
	if ($utilisateur3){ $res .= "<div>"._T('asso:adherent_libelle_utilisateur3').": ".$utilisateur3. "</div>";}
	if ($utilisateur4){ $res .= "<div>"._T('asso:adherent_libelle_utilisateur4').": ".$utilisateur4. "</div>";}
	if ($remarques){ $res .= "<div>"._T('asso:adherent_libelle_remarques').": ".$remarques. "</div>";}
	$res .= "</td>"
	.  "<td>";

// Afficher le bouton d'affichage du formulaire...
	if (statut_modifiable_auteur_supp($id_auteur, $auteur)) {
		$ancre = "legender_auteur_supp-$id_auteur";
		$clic = _T("asso:infos_supp");
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