<?php
#-------------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                                 #
#  File    : exec/spipbb_affecter_thread                            #
#  Authors : scoty 2007                                             #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs          #
#  Source  : gaf_affect.php                                         #
#  Contact : Hugues AROUX scoty!@!koakidi!.!com                     #
# [fr] page deplacer thread | ( anc. gaf_affect.php)                #
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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_affecter_thread()
{
	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;

	# initialiser spipbb
	include_spip('inc/spipbb_init');

	# requis de cet exec
	include_spip("inc/spipbb_inc_affecter");
	#

	$id_article = intval(_request('id_article'));
	$id_sujet = intval(_request('id_sujet'));

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin", '');

	echo "<a name='haut_page'></a>";


	debut_gauche();
		spipbb_menus_gauche(_request('exec'),$id_salon,$id_article);

	debut_droite();

	# pour admin seul
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_cadre_relief("");
		echo _T('avis_non_acces_page');
		fin_cadre_relief();
		echo fin_gauche(), fin_page();
		exit;
	}

	// details du sujet à déplacer
	$row = sql_fetsel("titre, auteur, statut, DATE_FORMAT(date_heure, '%d/%m/%Y %H:%i') AS dateur_sujet",
					"spip_forum",
					"id_forum=$id_sujet");
	$titre_sujet = $row['titre'];
	$auteur_sujet = $row['auteur'];
	$date_sujet = $row['dateur_sujet'];
	$statut_sujet = $row['statut'];

	if ($statut_sujet=="off") {
		$mis_a_off = "<div style='float:right;' title='"._T('Sujet rejete')."'>".
		http_img_pack("supprimer.gif",'x','')."</div>";
	}

	// nbre de posts reponses de ce sujet
	$row = spip_fetsel("COUNT(*) as cnt",
						"spip_forum",
						"id_thread='$id_sujet' AND statut IN ('publie', 'off', 'prop') AND id_parent!='0'");
	$nbr_post = $row['cnt'];

	$coul_sujet = $couleur_claire;

	echo "<br />";
	gros_titre(_T('gaf:deplacer_thread_suivant'));
	echo "<br />";

	debut_bloc_couleur($coul_sujet);
	echo "\n<table cellpadding='1' cellspacing='2' border='0' width='600'>\n";
	echo "<tr width='100%'>\n";
	echo "<td width='7%' valign='top' class='verdana2'>\n";
	echo "<img src='"._DIR_IMG_SPIPBB."gaf_sujet.gif' /><br />";
	echo $id_sujet."</td>\n";
	echo "<td width='' valign='top'>".$mis_a_off."";
	echo "<span class='verdana3'><b>".propre($titre_sujet)."</b></span><br />\n";
	echo "<span class='verdana2'>par <b>".$auteur_sujet."</b> .. ".
		_T('gaf:le')." ".$date_sujet."<span></td>\n";
	echo "<td width='8%' valign='top' class='verdana2'>\n";
	
	debut_bloc_gricont();
	echo "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' valign='absmiddle' /><br />\n";
	echo $nbr_post;
	fin_bloc();
	
	echo "</td>\n";
	echo "<td width='20%' valign='top' class='verdana2'>\n";
	echo "</td></tr>\n";
	echo "</table>\n";
	fin_bloc();

	// toute la hierarchie du(es) secteur(s)

	// trouver rubrique secteur (hotel) des forums
	$res_rs = sql_select("smr.id_rubrique, sr.titre, sr.descriptif",
						"spip_mots_rubriques smr LEFT JOIN spip_rubriques sr ON sr.id_rubrique = smr.id_rubrique",
						"smr.id_mot = ".$GLOBALS['spipbb']['id_secteur'] );

	echo "<div class='verdana3' style='padding:4px;'><b>"._T('gaf:forum_selection')."</b></div>";
	echo "<form action='".generer_url_ecrire("spipbb_affecter_affiche")."' method='post'>";

	while ($row=sql_fetch($res_rs)) {
		$id_hotel = $row['id_rubrique'];
		$titre_hotel = $row['titre'];
		$rang_rub=0;
		$retrait = 20*$rang_rub;
		
		debut_ligne_foncee($retrait);
		echo http_img_pack("secteur-12.gif","sect"," align='absmiddle'");
		echo "&nbsp;<span class='verdana2'>".$id_hotel."</span> - <b>".propre($titre_hotel)."</b>\n";
		fin_bloc();

		grand_ma($id_hotel, $rang_rub, $id_article);
		bb_article($id_hotel, $rang_rub, $id_article);
	}

	echo "<input type='hidden' name='titre_sujet' value='".typo($titre_sujet)."' />";
	echo "<input type='hidden' name='id_sujet' value='".$id_sujet."' />";
	echo "<input type='hidden' name='id_art_orig' value='".$id_article."' />";
	echo "</form>";


	// retour haut de page
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // exec_spipbb_affecter_thread
?>
