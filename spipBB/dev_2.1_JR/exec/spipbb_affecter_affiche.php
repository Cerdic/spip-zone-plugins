<?php
#-------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                 #
#  File    : inc/spipbb_affecter_affiche                            #
#  Authors : scoty 2007                                             #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs          #
#  Contact : Hugues AROUX scoty!@!koakidi!.!com                     #
# [fr] Page deplacer un thread resultat | (anc. gaf_val_affect.php) #
#-------------------------------------------------------------------#
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

#########################
# h. pour le moment ce script fait la mise à jour
# mais il faudra la passer en action !!
#########################

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

function exec_spipbb_affecter_affiche() {

	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;


	# initialiser spipbb
	include_spip('inc/spipbb_init');

	# requis de cet exec
	#

	$id_sujet = intval(_request('id_sujet'));
	$id_art_orig = intval(_request('id_art_orig'));
	$id_art_new = intval(_request('id_art_new'));
	$titre_sujet = _request('titre_sujet');

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin", '');

	echo "<a name='haut_page'></a>";


	debut_gauche();
		spipbb_menus_gauche(_request('exec'),$id_salon, $id_art);
		

	debut_droite();


	# admin seul
		if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
			debut_cadre_relief("");
			echo _T('avis_non_acces_page');
			fin_cadre_relief();
			echo fin_gauche(), fin_page();
			exit;
		}


	// cherche message du thread a deplacer
	$req=sql_select("id_forum",
					"spip_forum",
					"id_thread=$id_sujet AND id_article=$id_art_orig");

	while ($row=sql_fetch($req)) {
		$idf = $row['id_forum'];
		@sql_update("spip_forum",array('id_article' => $id_art_new),"id_forum=$idf");
	}

	// recupere info pour affichage
	$ro=sql_fetsel("titre","spip_articles","id_article=$id_art_orig");
	$titre_orig = $ro['titre'];

	$rn=sql_fetsel("titre","spip_articles","id_article=$id_art_new");
	$titre_new = $rn['titre'];

	debut_cadre_relief("");
		debut_ligne_foncee('0');
		echo "<img src='"._DIR_IMG_SPIPBB."gaf_sujet.gif' align='absmiddle' />\n";
		echo "<b>".propre($titre_sujet)."</b>\n";
		fin_bloc();
		
		echo "<div class='verdana3' style='padding:3px;'>"._T('gaf:forum_deplace')."</div>\n";
			
		debut_ligne_grise('30');
		echo "<div style='float:right; padding:3px; text-align:right; 
				border:2px solid ".$couleur_claire."; -moz-border-radius:5px;'>\n";
		echo " "._T('icone_retour')." <a href='".generer_url_ecrire("spipbb_forum","id_article=".$id_art_orig)."'>
				<img src='"._DIR_IMG_SPIPBB."gaf_forum.gif' border='0' align='absmiddle' /></a>";
		echo "</div>\n";
		echo propre($titre_orig);
		echo "<div style='clear:both;'></div>";
		fin_bloc();
		
		echo "<div class='verdana3' style='padding:3px;'>"._T('gaf:forum_vers')."</div>\n";

		debut_ligne_grise('30');
		echo "<div style='float:right; padding:3px; text-align:right; 
				border:2px solid ".$couleur_claire."; -moz-border-radius:5px;'>\n";
		echo " "._T('icone_retour')." <a href='".generer_url_ecrire("spipbb_forum", "id_article=".$id_art_new)."'>
				<img src='"._DIR_IMG_SPIPBB."gaf_forum.gif' border='0' align='absmiddle' /></a>";
		echo "</div>\n";
		echo propre($titre_new);
		echo "<div style='clear:both;'></div>\n";
		fin_bloc();

		echo "<br />";
		
		debut_bloc_gricont();
			echo "<span class='verdana3'>"._T('gaf:info_fin_maintenance')."</span>\n";
		fin_bloc();
		
	fin_cadre_relief();


	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();

} // exec_spipbb_affecter_affiche
?>
