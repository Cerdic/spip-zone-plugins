<?php
#-------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                 #
#  File    : exec/spipbb_rubriques_edit                             #
#  Authors : scoty 2007                                             #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs          #
#  Source  : base sur le script spip : rubrique_edit                #
#  Contact : Hugues AROUX scoty!@!koakidi!.!com                     #
# [fr] page sujet/thread                                            #
#-------------------------------------------------------------------#

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',3,__FILE__);

include_spip('inc/documents');

// ------------------------------------------------------------------------------
// source http://doc.spip.org/@exec_rubriques_edit_dist
// ------------------------------------------------------------------------------
function exec_spipbb_rubriques_edit_dist() {

	global
	  $champs_extra,
	  $connect_statut,
	  $id_parent,$id_rubrique,
	  $new,$options;

	# initialiser spipbb !!
	include_spip('inc/spipbb_init');
	


	if ($new == "oui") {
		$id_rubrique = 0;
		$titre = filtrer_entites(_T('titre_nouvelle_rubrique'));
		$onfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		$descriptif = "";
		$texte = "";
		$id_parent = intval($id_parent);

		if (!autoriser('creerrubriquedans','rubrique',$id_parent)) {
			$id_parent = reset($GLOBALS['connect_id_rubrique']);
		}
	} else {
		$id_rubrique = intval($id_rubrique);

		$row = sql_fetsel("id_parent, titre, descriptif, texte, id_secteur, extra","spip_rubriques","id_rubrique='$id_rubrique'");

		if (!$row) exit;

		$id_parent = $row['id_parent'];
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$texte = $row['texte'];
		$id_secteur = $row['id_secteur'];
		$extra = $row["extra"];
	}
	$commencer_page = charger_fonction('commencer_page', 'inc');

	if ($connect_statut !='0minirezo'
	OR ($new=='oui' AND !autoriser('creerrubriquedans','rubrique',$id_parent))
	OR ($new!='oui' AND !autoriser('modifier','rubrique',$id_rubrique)))  {
		echo $commencer_page(_T('info_modifier_titre', array('titre' => $titre)), "naviguer", "rubriques", $id_rubrique);
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		echo fin_page();
		exit;
	}

	pipeline('exec_init',array('args'=>array('exec'=>'rubriques_edit','id_rubrique'=>$id_rubrique),'data'=>''));
	
# h. => indiquer forum, spipbb_admin
	echo $commencer_page(_T('info_modifier_titre', array('titre' => $titre)), "forum", "spipbb_admin", $id_rubrique);

	if ($id_parent == 0) $ze_logo = "secteur-24.gif";
	else $ze_logo = "rubrique-24.gif";

	if ($id_parent == 0) $logo_parent = "racine-site-24.gif";
	else {
		$id_secteur = sql_fetsel("id_secteur","spip_rubriques","id_rubrique='$id_parent'");
		$id_secteur = $id_secteur['id_secteur'];
		if ($id_parent == $id_secteur)
			$logo_parent = "secteur-24.gif";
		else	$logo_parent = "rubrique-24.gif";
	}

	
	debut_gauche();
		spipbb_menus_gauche(_request('exec'),$id_salon);


	# h. pas de doc associes par interface GAF
	/*
	// Pave "documents associes a la rubrique"
	if (!$new){
		# affichage sur le cote des pieces jointes, en reperant les inserees
		# note : traiter_modeles($texte, true) repere les doublons
		# aussi efficacement que propre(), mais beaucoup plus rapidement
		traiter_modeles(join('',$row), true);
		echo afficher_documents_colonne($id_rubrique, 'rubrique');
	}
	*/

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'rubriques_edit','id_rubrique'=>$id_rubrique),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'rubriques_edit','id_rubrique'=>$id_rubrique),'data'=>''));	  
	debut_droite();

	debut_cadre_formulaire();

	echo "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
	echo "<tr>";
	echo "<td>";

#h. retour SPIPBB
	if ($id_rubrique) icone(_T('icone_retour'), generer_url_ecrire("spipbb_admin","id_salon=$id_rubrique"), $ze_logo, "rien.gif");
	else icone(_T('icone_retour'), generer_url_ecrire("spipbb_admin","id_salon=$id_parent"), $ze_logo, "rien.gif");

	echo "</td>";
	echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
	echo "<td style='width: 100%'>";
	echo _T('info_modifier_rubrique');
	gros_titre($titre);
	echo "</td></tr></table>";

	$titre = entites_html($titre);
	$chercher_rubrique = charger_fonction('chercher_rubrique', 'inc');

	$form = _T('entree_titre_obligatoire')
	.  "<input type='text' class='formo' name='titre' value=\"$titre\" size='40' $onfocus />"
	. debut_cadre_couleur("$logo_parent", true, '', _T('entree_interieur_rubrique').aide ("rubrub"))
	. $chercher_rubrique($id_parent, 'rubrique', !$connect_toutes_rubriques, $id_rubrique);

// si c'est une rubrique-secteur contenant des breves, demander la
// confirmation du deplacement
	 $contient_breves = sql_fetsel("COUNT(*) AS cnt","spip_breves","id_rubrique='$id_rubrique' LIMIT 1");

	 $contient_breves = $contient_breves['cnt'];

	if ($contient_breves > 0) {
		$scb = ($contient_breves>1? 's':'');
		$scb = _T('avis_deplacement_rubrique',
			array('contient_breves' => $contient_breves,
			      'scb' => $scb));
		$form .= "<div><span class='spip_small'><input type='checkbox' name='confirme_deplace' value='oui' id='confirme-deplace' /><label for='confirme-deplace'>&nbsp;" . $scb . "</span></label></div>\n";
	} else
		$form .= "<input type='hidden' name='confirme_deplace' value='oui' />\n";

	$form .= fin_cadre_couleur(true)
	. "<br />";

	if ($options == "avancees" OR $descriptif) {
		$form .= "<b>"._T('texte_descriptif_rapide')."</b><br />"
		. _T('entree_contenu_rubrique')."<br />"
		. "<textarea name='descriptif' class='forml' rows='4' cols='40'>"
		. entites_html($descriptif)
		. "</textarea>\n";
	}

	$form .= "<b>"._T('info_texte_explicatif')."</b>"
	. aide ("raccourcis")
	. "<br /><textarea name='texte' rows='15' class='formo' cols='40'>"
	. entites_html($texte)
	. "</textarea>\n";

	if ($champs_extra) {
		include_spip('inc/extra');
		$form .= extra_saisie($extra, 'rubriques', $id_secteur);
	}

	$form .= "\n<p align='right'><input type='submit' value='"
	. _T('bouton_enregistrer')
	. "' class='fondo' />\n</p>";

# h. spipbb_editer_rubrique -- ret: gaf_admin
	echo redirige_action_auteur("spipbb_editer_rubrique", $id_rubrique ? $id_rubrique : 'oui', 'spipbb_admin', '', $form, " method='post'");

	echo fin_cadre_formulaire();

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'rubriques_edit','id_rubrique'=>$id_rubrique),'data'=>''));	  

	# pied page exec
	bouton_retour_haut();
	
	echo fin_gauche(), fin_page();
}
?>
