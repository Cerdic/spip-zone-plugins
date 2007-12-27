<?php

#---------------------------------------------------------------#
#  Plugin  : migre_static - Licence : GPL                       #
#  File    : exec/migre_formulaire - first step form            #
#  Authors : Chryjs, 2007 - Beurt, 2006                         #
#  Contact : chryjs�@!free�.!fr                                 #
# [fr] Cette page sert a initialiser les valeurs pour le plugin #
# [fr] avant le lancement de l import des pages                 #
# [en] This webpage gets required values before                 #
# [en] starting the migration process                           #
#---------------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/migre");
include_spip("inc/editer_article");

// [fr] compatibilite spip 1.9
// [en] SPIP 1.9 compatibility
if ($GLOBALS['spip_version_code']<1.92) { function fin_gauche(){return false;} }

if (!function_exists('spip_num_rows')) { include_spip("inc/vieilles_defs"); }

// ------------------------------------------------------------------------------
// [fr] Methode exec
// [fr] Affiche la page complete spip privee avec le formulaire
// [en] Exec method
// [en] Provides the full spip private space form
// ------------------------------------------------------------------------------
function exec_migre_formulaire()
{
	global $connect_statut;
	$id_rubrique = intval(_request('id_rubrique'));

	// [fr] Pour le moment l acces est reserve a l administrateur, a voir plus tard
	// [fr] pour tester plutot en fonction rubrique de l import comme pour les articles...
	// [en] For now the access is only allowed to the admin, will check it later
	// [en] in order to check it for each rubrique like for the articles...

	if ($connect_statut != '0minirezo') {
		$err = "<strong>"._T('avis_non_acces_page')."</strong>";
		include_spip('inc/minipres');
		echo minipres($err);
	} else {

		// [fr] initialisations
		// [en] initialize
		if (!function_exists('filtrer_entites')) include_spip('inc/filtres');
		$row['titre'] = filtrer_entites(_T('info_nouvel_article'));
		if (!isset($GLOBALS['meta']['migrestatic'])) migre_static_init_metas() ;
	
		// [fr] La conf pre-existante domine
		// [en] Pre-existing config leads
		$row['id_rubrique'] = (!empty($GLOBALS['migrestatic']['migre_id_rubrique'])) ? $GLOBALS['migrestatic']['migre_id_rubrique'] : $id_rubrique;
		if (!$row['id_rubrique']) {
			if ($connect_id_rubrique)
				$row['id_rubrique'] = $id_rubrique = $connect_id_rubrique[0];
			else {
				$row_rub = spip_fetch_array(spip_query("SELECT id_rubrique FROM spip_rubriques ORDER BY id_rubrique DESC LIMIT 1"));
				$row['id_rubrique'] = $id_rubrique = $row_rub['id_rubrique'];
			}
			if (!autoriser('creerarticledans','rubrique',$row['id_rubrique'] )){
				// [fr] manque de chance, la rubrique n'est pas autorisee, on cherche un des secteurs autorises
				// [en] too bad , this rubrique is not allowed, we look for the first allowed sector
				$res = spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0");
				while (!autoriser('creerarticledans','rubrique',$row['id_rubrique'] ) && $row_rub = spip_fetch_array($res)){
					$row['id_rubrique'] = $row_rub['id_rubrique'];
				}
			}
		}
		// [fr] recuperer les donnees du secteur
		// [en] load the sector datas
		$row_rub = spip_fetch_array(spip_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique=$id_rubrique"));
		$row['id_secteur'] = $row_rub['id_secteur'];
		$id_rubrique = $row['id_rubrique'];
	
		if (!$row
		OR !autoriser('creerarticledans','rubrique',$id_rubrique)) {
			$err = "<strong>"._T('avis_non_acces_page')."</strong>";
			include_spip('inc/minipres');
			echo minipres($err);
		} else {
			// [fr] compatibilite ascendante
			// [en] upscale compat
			if ($GLOBALS['spip_version_code']<1.92)
				debut_page(_T('migrestatic:titre_migre_formulaire'), 'configuration', 'migre_static');
			else {
				$commencer_page = charger_fonction('commencer_page', 'inc');
				echo $commencer_page(_T('migrestatic:titre_migre_formulaire'), "configuration", 'migre_static');
			}
	
			echo debut_grand_cadre(true);
			echo afficher_hierarchie($id_rubrique);
			echo fin_grand_cadre(true);
			echo debut_gauche('',true);
		
			echo creer_colonne_droite('',true);
			echo debut_droite('',true);
		
			echo debut_cadre_formulaire('',true);
			echo migre_gen_top($id_rubrique,_T('migrestatic:titre_migre_formulaire'));
			echo migre_formulaire($row);
			echo fin_cadre_formulaire(true);
			echo fin_gauche(), fin_page();
		} // !row or !autoriser
	} // ! acces Ominirezo
} // exec_migre_formulaire

// ------------------------------------------------------------------------------
// [fr] Genere le haut de bloc du formulaire de migration
// [en] Generates the top of the migration form block
// ------------------------------------------------------------------------------
function migre_gen_top($id_rubrique,$titre) {
	$go_back=generer_url_ecrire("naviguer","id_rubrique=$id_rubrique");
	return
		"\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>" .
		"<tr>".
		"\n<td style='width: 130px;'>" .
		icone(_T('icone_retour'), $go_back, "article-24.gif", "rien.gif", ' ',false) .
		"</td>\n<td>" .
		"<img src='" .
		_DIR_IMG_PACK . "rien.gif' width='10' alt='' />" .
		"</td>\n<td>" .
		_T('migrestatic:sur_titre_migre_formulaire') .
		gros_titre($titre,'',false) .
		"</td></tr></table>\n" .
		"<hr />\n";
} // migre_gen_top

// ------------------------------------------------------------------------------
// [fr] Genere le formulaire de saisie des parametres de migration
// [en] Generates the form to fill with migration parameters
// ------------------------------------------------------------------------------
function migre_formulaire($row=array())
{
	$aider = charger_fonction('aider', 'inc');
	$config = "";
	$id_rubrique = $row['id_rubrique'];
	$id_secteur = $row['id_secteur'];
	$valeur_url= (!empty($GLOBALS['migrestatic']['migre_liste_pages'])) ? $GLOBALS['migrestatic']['migre_liste_pages'] : _T('migrestatic:liste_des_pages') ;
	$valeur_test= (!empty($GLOBALS['migrestatic']['migre_test'])) ? $GLOBALS['migrestatic']['migre_test'] : "checked" ;
	
	if (!function_exists('editer_article_rubrique')) {
		$chercher_rubrique=charger_fonction('chercher_rubrique','inc');
		$selrub=$chercher_rubrique($id_rubrique, 'article', false);

	}
	else $selrub=editer_article_rubrique($id_rubrique, $id_secteur, $config, $aider);
	
	$form= "\n".
		$selrub .
		"\n<p><b>" .
		_T('migrestatic:choix_mot_cle') .
		"</b>\n<br>" ._T('migrestatic:sous_choix_mot_cle') ."<br />".
		choisir_un_mot($id_rubrique) ."</p>".
		"\n<p><b>" .  _T('migrestatic:choix_url_listepages') .  "</b>\n<br>" . _T('migrestatic:sous_choix_url_listepages') .
		"<br />\n<input type='text' name='form_liste_pages' class='forml' ".
		" value=\"" . $valeur_url .
		"\" size='128' /></p>" .
		"\n<p><b>" .  _T('migrestatic:choix_test') .  "</b>" .
		"\n<br>" .  _T('migrestatic:sous_choix_test') .
		"\n<input type='checkbox' name='form_migre_test' id='form_migre_test' checked='".$valeur_test .
		"' class='check' /></p>" .
		"<div align='right'><input class='fondo' type='submit' value='" .
		_T('bouton_valider') .
		"' /></div>" .
		"\n<input type='hidden' name='etape' id='etape' value='1'>" .
		formulaire_liste_balise()  ;

	return generer_action_auteur("migre_action", $id_rubrique, $retour, $form, " method='post' name='formulaire'");
} // migre_formulaire

// ------------------------------------------------------------------------------
// [fr] Selection de mot-cles globaux pour tous les articles migres
// [en] Select keywords for all imported articles
// ------------------------------------------------------------------------------
function choisir_un_mot($id_rubrique)
{
	$res.="\n<select name='form_idmot[]' multiple='multiple' size='10' id='form_idmot' style='width:300px;'>\n";
	// [fr] Rend possible de ne pas choisir un mot cle
	// [en] Allows not to select a keyword
	$result = spip_query("SELECT mots.id_mot, mots.titre FROM spip_mots AS mots, spip_groupes_mots AS groupe WHERE mots.id_groupe=groupe.id_groupe AND groupe.articles='oui' ORDER BY mots.id_mot");
	if (spip_num_rows($result) > 0) {
		while ($row = spip_fetch_array($result)) {
			$id_mot = $row['id_mot'];
			$titre_mot = $row['titre'];
			$valeur_select = (is_array($GLOBALS['migrestatic']['migre_id_mot']) AND in_array($id_mot,$GLOBALS['migrestatic']['migre_id_mot']) ) ? " selected='selected' " : "" ;
			$res.="<option value='".$row['id_mot']."' ".$valeur_select ."> ".$id_mot.". ".typo($row['titre'])."</option>\n";
		}
	}
	$res.="</select>\n" ;
	return $res;
} // choisir_un_mot

// ------------------------------------------------------------------------------
// [fr] Produit un formulaire avec une liste de balises HTML et leur eventuelle conversion
// [en] Provides a form showing a list of HTML marks and their translation
// ------------------------------------------------------------------------------
function formulaire_liste_balise()
{
	if ($GLOBALS['migrestatic'] AND is_array($GLOBALS['migrestatic']['migre_htos'])) {
		$htos=$GLOBALS['migrestatic']['migre_htos'];
	}
	else {
		$htos=get_list_htos();
	}
	// fwn
	$valeur_debut = ($GLOBALS['migrestatic']['migre_bcentredebut']) ? $GLOBALS['migrestatic']['migre_bcentredebut']: ""; // was : &lt;.{3,5}NAME.*index.{3,5}&gt;
	$valeur_fin = ($GLOBALS['migrestatic']['migre_bcentrefin']) ? $GLOBALS['migrestatic']['migre_bcentrefin']: ""; // was : &lt;.{3,5}END.*index.*&gt;
	// fwn

	$res  = debut_cadre_relief("",true);
	$res .= bouton_block_invisible('migrefiltre') ;
	$res .= "<b class='arial2'>". _T('migrestatic:choix_balises')."</b>" ;
	$res .= debut_block_invisible('migrefiltre') ;
	$res .= "<p class='arial2'>"._T('migrestatic:sous_choix_balises')."</p>" ;

	$res .= "\n<p><b>" .  _T('migrestatic:choix_balise_centre_debut') .  "</b>\n<br>" . _T('migrestatic:sous_choix_balise_centre_debut') ;
	$res .= "<br />\n<input type='text' name='form_bcentredebut' class='forml' value='".$valeur_debut."' size='128' /></p>" ;
	$res .= "\n<p><b>" .  _T('migrestatic:choix_balise_centre_fin') .  "</b>\n<br>" . _T('migrestatic:sous_choix_balise_centre_fin') ;
	$res .= "<br />\n<input type='text' name='form_bcentrefin' class='forml' value='".$valeur_fin."' size='128' /></p>" ;

	if (count($htos)>0) {
		reset($htos);
		$res .= "\n<table cellpadding='0' cellspacing='4' border='0' width='100%' class='arial2'>\n";
		$res .= "<tr><td></td><td>"._T('migrestatic:choix_balises_filtre')."</td><td>"._T('migrestatic:choix_balises_htos')."</td></tr>" ;
		while ( list($key,$val) = each($htos) ) {
			$res .= "\n<tr>\n<td><b>"._T('migrestatic:choix_balises_'.$key)."</b></td>";
			$res .= "\n<td>\n<input type='text' name='".$key."-filtre' class='forml' value='".htmlspecialchars($val['filtre'])."' size='50' /></td>" ;
			$res .= "\n<td><input type='text' name='".$key."-htos' class='forml' value='".htmlspecialchars($val['spip'])."' size='20' /></td></tr>" ;
		}
		$res .="</table>";
	}

	include_spip("inc/plugin");
	$plug=liste_plugin_actifs();
	if ( is_array($plug) 
		AND array_key_exists("COUTEAU_SUISSE",$plug)
		AND isset($GLOBALS['meta']['cs_decoupe']) )
	{
		$valeur_cs_decoupe= (!empty($GLOBALS['migrestatic']['migre_cs_decoupe'])) ? $GLOBALS['migrestatic']['migre_cs_decoupe'] : "checked" ;
		$res.= "\n<p><b>" .  _T('migrestatic:config_cs_decoupe') .  "</b><br />" ._T('migrestatic:sous_choix_cs_decoupe');
		$res.= "\n<input type='checkbox' name='form_migre_cs_decoupe' id='form_migre_cs_decoupe' checked='".$valeur_cs_decoupe . "' class='check' />" ;
	}

	$res .="\n<div align='right'><input class='fondo' type='submit' value='" . _T('bouton_valider') . "' /></div>" ;
	$res .= fin_block();
	$res .= fin_cadre_relief(true);
	return $res;
} // formulaire_liste_balise

?>
