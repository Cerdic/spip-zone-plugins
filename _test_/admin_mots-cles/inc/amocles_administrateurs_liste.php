<?php

	// inc/amocles_administrateurs_liste.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	Amocles est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
/***************************************************************************\
 * Certains éléments ici sont directement extraits de SPIP 192c
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');

// L'ajout d'un auteur se fait par mini-navigateur dans la fourchette:
define('_SPIP_SELECT_MIN_AUTEURS', 10); // en dessous: balise Select
define('_SPIP_SELECT_MAX_AUTEURS', 100); // au-dessus: saisie + return

// CP-20071105 - Adapté de inc_editer_auteurs_dist()
function amocles_liste_admins_groupes_mots($type, $id, $flag, $cherche_auteur, $ids, $titre_boite, $script_edit_objet) {
	global $options;

	$cond_les_auteurs = $arg_ajax = "";
	$aff_les_auteurs = afficher_auteurs_objet($type, $id, $flag, $cond_les_auteurs, $script_edit_objet, $arg_ajax);
	
	if ($flag AND $options == 'avancees') {
		$futurs = amocles_ajouter_auteurs_objet($type, $id, $cond_les_auteurs, $script_edit_objet, $arg_ajax);
	} else $futurs = '';

	$ldap = isset($GLOBALS['meta']['ldap_statut_import']) ?
	  $GLOBALS['meta']['ldap_statut_import'] : '';

	return amocles_editer_auteurs_objet($type, $id, $flag, $cherche_auteur, $ids, $aff_les_auteurs, $futurs, $ldap,$titre_boite,$script_edit_objet, $arg_ajax);
}

// CP-20071105 - Adapté de editer_auteurs_objet()
function amocles_editer_auteurs_objet($type, $id, $flag, $cherche_auteur, $ids, $les_auteurs, $futurs, $statut, $titre_boite,$script_edit_objet, $arg_ajax)
{
	global $spip_lang_left, $spip_lang_right, $options;

	$bouton_creer_auteur =  $GLOBALS['connect_toutes_rubriques'];
	$clic = _T('icone_creer_auteur');

//
// complement de action/editer_auteurs.php pour notifier la recherche d'auteur
//
	if ($cherche_auteur) {
		$reponse = ""
		. "<div style='text-align: $spip_lang_left'>"
		. debut_boite_info(true)
		. rechercher_auteurs_objet($cherche_auteur, $ids, $type, $id, $script_edit_objet, $arg_ajax)
		. fin_boite_info(true)
		. '</div>';
	} else $reponse ='';

	$reponse .= $les_auteurs;

//
// Ajouter un auteur
//

	$res = '';
	if ($flag && ($options == 'avancees')) {

		$res = "<div style='float:$spip_lang_right; width:280px;position:relative;display:inline;'>"
		. $futurs
		."</div>\n"
		. $res;
	}

	// petit triangle à déplier
	$bouton = ""
		.	(
			(!$flag)
		   ? ''
		   : 
				(
				($flag === 'ajax')
				? bouton_block_visible("auteurs$type")
				: bouton_block_invisible("auteurs$type")
				)
			)
		. $titre_boite
		;

	$res = '<div><div>&nbsp;</div>' // pour placer le gif patienteur
	. debut_cadre_enfonce("", true, "", $bouton)
	. $reponse
	.  (
		($flag === 'ajax')
		?	debut_block_visible("auteurs$type") 
		: debut_block_invisible("auteurs$type")
		)
	. $res
	. fin_block()
	. fin_cadre_enfonce(true)
	. '</div>'
	;

	return ajax_action_greffe("editer_auteurs-$id", $res);
}

// http://doc.spip.org/@determiner_auteurs_objet
function determiner_auteurs_objet($type, $id, $cond='', $limit='')
{
	$les_auteurs = array();
	if (!preg_match(',^[a-z]*$,',$type)) {
		return $les_auteurs; 
	}
	
	$sql_in = implode(",", amocles_admins_groupes_mots_get_ids());

	$result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE id_auteur IN ($sql_in) "
	. ($limit? " LIMIT $limit": '')
	);

	return $result;
}

// http://doc.spip.org/@determiner_non_auteurs
function determiner_non_auteurs($type, $id, $cond_les_auteurs, $order)
{
	$cond = '';
	$res = determiner_auteurs_objet($type, $id, $cond_les_auteurs);
	if (spip_num_rows($res)<200){ // probleme de performance au dela, on ne filtre plus
		while ($row = spip_fetch_array($res))
			$cond .= ",".$row['id_auteur'];
	}
	if (strlen($cond))
		$cond = "id_auteur NOT IN (" . substr($cond,1) . ') AND ';

	return spip_query("SELECT * FROM spip_auteurs WHERE $cond" . "statut!='5poubelle' AND statut!='6forum' AND statut!='nouveau' ORDER BY $order");
}

// http://doc.spip.org/@rechercher_auteurs_objet
function rechercher_auteurs_objet($cherche_auteur, $ids, $type, $id, $script_edit_objet, $arg_ajax)
{
	if (!$ids) {
		return "<strong>"._T('texte_aucun_resultat_auteur', array('cherche_auteur' => $cherche_auteur)).".</strong><br />";
	}
	elseif ($ids == -1) {
		return "<strong>"._T('texte_trop_resultats_auteurs', array('cherche_auteur' => $cherche_auteur))."</strong><br />";
	}
	elseif (preg_match('/^\d+$/',$ids)) {

		$row = spip_fetch_array(spip_query("SELECT nom FROM spip_auteurs WHERE id_auteur=$ids"));
		return "<strong>"._T('texte_ajout_auteur')."</strong><br /><ul><li><span class='verdana1 spip_small'><strong><span class='spip_medium'>".typo($row['nom'])."</span></strong></span></li></ul>";
	}
	else {
		$ids = preg_replace('/[^0-9,]/','',$ids); // securite
		$result = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur IN ($ids) ORDER BY nom");

		$res = "<strong>"
		. _T('texte_plusieurs_articles', array('cherche_auteur' => $cherche_auteur))
		. "</strong><br />"
		.  "<ul class='verdana1'>";
		while ($row = spip_fetch_array($result)) {
				$id_auteur = $row['id_auteur'];
				$nom_auteur = $row['nom'];
				$email_auteur = $row['email'];
				$bio_auteur = $row['bio'];

				$res .= "<li><strong>".typo($nom_auteur)."</strong>";

				if ($email_auteur) $res .= " ($email_auteur)";

				$res .= " | "
				  .  ajax_action_auteur('editer_auteurs', "$id,$type,$id_auteur",$script_edit_objet,"id_{$type}=$id", array(_T('lien_ajouter_auteur')),$arg_ajax);

				if (trim($bio_auteur)) {
					$res .= "<br />".couper(propre($bio_auteur), 100)."\n";
				}
				$res .= "</li>\n";
			}
		$res .= "</ul>";
		return $res;
	}
}

// CP-20071105 - Adapté de afficher_auteurs_objet()
function afficher_auteurs_objet($type, $id, $flag_editable, $cond_les_auteurs, $script_edit, $arg_ajax)
{
	global $connect_statut, $options, $connect_id_auteur, $spip_display;
	
	
	$les_auteurs = array();
	if (!preg_match(',^[a-z]*$,',$type)) return $les_auteurs; 

	$result = determiner_auteurs_objet($type, $id, $cond_les_auteurs);
	$cpt = spip_num_rows($result);

	$tmp_var = "amocles_administrateurs-$id";
	$nb_aff = floor(1.5 * _TRANCHES);
	if ($cpt > $nb_aff) {
		$nb_aff = _TRANCHES; 
		//$tranches = afficher_tranches_requete($cpt, $tmp_var, generer_url_ecrire('editer_auteurs',$arg_ajax), $nb_aff);
		$tranches = afficher_tranches_requete($cpt, $tmp_var, generer_url_ecrire('amocles_administrateurs', $arg_ajax), $nb_aff);
	} else $tranches = '';
	
	$deb_aff = _request($tmp_var);
	$deb_aff = ($deb_aff !== NULL ? intval($deb_aff) : 0);
	
	$limit = (($deb_aff < 0) ? '' : "$deb_aff, $nb_aff");
	$result = determiner_auteurs_objet($type,$id,$cond_les_auteurs,$limit);

	// charger ici meme si pas d'auteurs
	// car inc_formater_auteur peut aussi redefinir determiner_non_auteurs qui sert plus loin
	if (!$formater_auteur = charger_fonction("formater_auteur_$type", 'inc',true))
		$formater_auteur = charger_fonction('formater_auteur', 'inc');

	if (!spip_num_rows($result)) return '';

	$table = array();

	while ($row = spip_fetch_array($result)) {
		$id_auteur = $row['id_auteur'];
		$vals = $formater_auteur($id_auteur);

		// lien retirer auteur
		if (
			$flag_editable
			&& ($id_auteur != 1) // ne pas proposer de supprimer admin 1. 
			&& ($connect_id_auteur != $id_auteur || $connect_statut == '0minirezo') 
			&& ($options == 'avancees')
			) {
			$vals[] =  ajax_action_auteur('amocles_administrateur'
				, "$id,$type,-$id_auteur"
				, $script_edit
				, "id_{$type}=$id"
				, array(_T('lien_retirer_auteur')."&nbsp;". http_img_pack('croix-rouge.gif', "X", " class='puce' style='vertical-align: bottom;'"))
				, $arg_ajax);
		} else  $vals[] = "";
		$table[] = $vals;
	}
	
	$largeurs = array('14', '', '', '', '', '');
	$styles = array('arial11', 'arial2', 'arial11', 'arial11', 'arial11', 'arial1');

	$t = afficher_liste($largeurs, $table, $styles);
	if ($spip_display != 4)
	  $t = $tranches
	  	. "<table width='100%' cellpadding='3' cellspacing='0' border='0'>"
	    . $t
	    . "</table>";
	return ("<div class='liste'>$t</div>\n");
}


// CP-20071105 - Adapté de ajouter_auteurs_objet()
function amocles_ajouter_auteurs_objet($type, $id, $cond_les_auteurs,$script_edit, $arg_ajax)
{

	if (!$determiner_non_auteurs = charger_fonction('determiner_non_auteurs_'.$type,'inc',true))
		$determiner_non_auteurs = 'determiner_non_auteurs';
	$query = $determiner_non_auteurs($type, $id, $cond_les_auteurs, "statut, nom");
	if (!$num = spip_num_rows($query)) return '';

	$js = "findObj_forcer('valider_ajouter_auteur').style.visibility='visible';";

	$text = "<span class='verdana1'><strong>"
	. _T('titre_cadre_ajouter_auteur')
	. "</strong></span>\n";

	if ($num <= _SPIP_SELECT_MIN_AUTEURS){
		$sel = "$text<select name='nouv_auteur' size='1' style='width:150px;' class='fondl' onchange=\"$js\">" .
		   objet_auteur_select($query) .
		   "</select>";
		$clic = _T('bouton_ajouter');
	} else if  ((_SPIP_AJAX < 1) OR ($num >= _SPIP_SELECT_MAX_AUTEURS)) {
		  $sel = "$text <input type='text' name='cherche_auteur' onclick=\"$js\" class='fondl' value='' size='20' />";
		  $clic = _T('bouton_chercher');
	} else {
	    $sel = selecteur_auteur_ajax($type, $id, $js, $text);
	    $clic = _T('bouton_ajouter');
	}

	return (
		ajax_action_post('amocles_administrateur', "$id,$type", $script_edit, "id_{$type}=$id"
			, $sel, $clic, "class='fondo visible_au_chargement' id='valider_ajouter_auteur'", "", $arg_ajax)
	);
}

// http://doc.spip.org/@objet_auteur_select
function objet_auteur_select($result)
{
	global $couleur_claire, $connect_statut ;

	$statut_old = $premiere_old = $res = '';

	while ($row = spip_fetch_array($result)) {
		$id_auteur = $row["id_auteur"];
		$nom = $row["nom"];
		$email = $row["email"];
		$statut = $row["statut"];

		$statut=str_replace("0minirezo", _T('info_administrateurs'), $statut);
		$statut=str_replace("1comite", _T('info_redacteurs'), $statut);
		$statut=str_replace("6visiteur", _T('info_visiteurs'), $statut);
				
		$premiere = strtoupper(substr(trim($nom), 0, 1));

		if ($connect_statut != '0minirezo')
			if ($p = strpos($email, '@'))
				  $email = substr($email, 0, $p).'@...';
		if ($email)
			$email = " ($email)";

		if ($statut != $statut_old) {
			$res .= "\n<option value=\"x\" />";
			$res .= "\n<option value=\"x\" style='background-color: $couleur_claire;'> $statut</option>";
		}

		if ($premiere != $premiere_old AND ($statut != _T('info_administrateurs') OR !$premiere_old))
			$res .= "\n<option value=\"x\" />";
				
		$res .= "\n<option value=\"$id_auteur\">&nbsp;&nbsp;&nbsp;&nbsp;" . supprimer_tags(couper(typo("$nom$email"), 40)) . '</option>';
		$statut_old = $statut;
		$premiere_old = $premiere;
	}
	return $res;
}

// CP-20071105 - Adapté de selecteur_auteur_ajax ()
function selecteur_auteur_ajax($type, $id, $js, $text)
{
	include_spip('inc/chercher_rubrique');
	$url = generer_url_ecrire('selectionner_auteur');

	return $text . construire_selecteur($url, $js, 'selection_auteur', 'nouv_auteur', ' type="hidden"');
}
?>
