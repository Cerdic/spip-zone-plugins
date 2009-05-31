<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : exec/spipbb_effacer                                #
#  Authors : scoty 2007                                         #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : Hugues AROUX scoty!@!koakidi!.!com                 #
# [fr] Page Effacer des posts                                   #
# [en] delete posts                                             #
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
if (!defined("_INC_SPIPBB_COMMON")) include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

# initialiser spipbb
include_spip('inc/spipbb_init');
# pour le javascript de (de)selection
include_spip('inc/spipbb_inc_formpost');

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function exec_spipbb_effacer() {
	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee,
			$spip_display;

	# requis de cet exec
	#

	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipbb:admin_titre_page_'._request('exec')), "forum", "spipbb_admin",'');
	echo "<a name='haut_page'></a>";

	echo debut_gauche('',true);
		spipbb_menus_gauche(_request('exec'));

	echo debut_droite('',true);

	// réservé au Admins
		if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
			debut_cadre_relief("");
			echo _T('avis_non_acces_page');
			fin_cadre_relief();
			echo fin_gauche(), fin_page();
			exit;
		}

	// effacer définitivement les posts inscrit à "off"
	if($action=='efface_select') {
		$tbl_eraze=$_POST['eraze'];
		//suppresion des posts selectionnés
		debut_cadre_relief("poubelle.gif");
		echo gros_titre(_T('spipbb:admin_titre_page_'._request('exec')),'',false);

		$nbr_eraze = count($tbl_eraze);
		if ($nbr_eraze==0) {
			echo "<div class='verdana3'><b>"._T('spipbb:post_aucun_pt')."</b></div>";
		}
		else {
			foreach($tbl_eraze as $id)
				{ $req = sql_delete("spip_forum","id_forum=$id and statut='off'"); }
			echo "<div class='verdana3'>".$nbr_eraze._T('spipbb:posts_effaces')."</div>";
		}
		fin_cadre_relief();
	}

	$res=sql_select("id_forum,id_parent,titre,id_thread,COUNT(id_forum) as total_post",
					"spip_forum",
					"statut = 'off'",
					"id_thread");

	debut_cadre_relief("");
	echo "<form action='".generer_url_ecrire("spipbb_effacer")."' method='post' name='formeffacer'>\n";
	echo "<input type='hidden' name='action' value='efface_select'>\n";
	echo "<table cellpadding='3' cellspacing='0' border='0' width='100%'>\n";
	echo "<tr width='100%'>\n";
	echo "<td height='35' valign='top' colspan='2'>\n";
	echo gros_titre(_T('spipbb:posts_refuses'),'',false);
	echo "</td></tr>";

	$ifond=0;

	while ($row=sql_fetch($res)) {
		$id_post = $row['id_forum'];
		$id_parent = $row['id_parent'];
		$id_thread = $row['id_thread'];
		$titre = $row['titre'];
		$total_post = $row['total_post'];

		$ico_ligne = $id_parent=='0' ? "gaf_sujet-12.gif" : "gaf_post-12.gif";
		$retrait = $id_parent=='0' ? "5" : "20";

		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? $couleur_claire : '#e3e3e3';

		echo "<tr class='verdana2' bgcolor='".$couleur."'><td><div style='margin-left:".$retrait."px;'>\n";
		echo "<img src='"._DIR_IMG_SPIPBB.$ico_ligne."' /> ".$id_post." - ".propre($titre);
		echo "&nbsp;<a href='".url_post_tranche($id_post, $id_thread)."'>&nbsp;".
			http_img_pack("plus.gif",'ico',"align='absmiddle' border='0'",_T('spipbb:voir')).
			"</a>";
		echo "</div>";
		echo "</td><td valign='absmiddle'>\n";
		echo "<input type='checkbox' name='eraze[]' value='".$id_post."'>";
		echo "</td></tr>\n";
		if ($total_post>'1') {
			$res2=sql_select("id_forum,titre",
							"spip_forum",
							"id_thread=$id_thread AND statut='off' AND id_forum!=$id_post");

			if($id_parent=='0')
				{
				$nbr_post=sql_count($res2);
				echo "<tr class='verdana2' bgcolor='".$couleur."'><td>\n";
				echo "<div class='verdana2' style='color:#ED4242; margin-left:30px; padding:2px;'>\n";
				echo "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' align='absmiddle' />&nbsp;";
				echo _T('spipbb:post_efface_lui', array('nbr_post'=>$nbr_post) );
				echo "<a href='".generer_url_ecrire("spipbb_sujet","id_sujet=".$id_post)."'>&nbsp;".
						http_img_pack("plus.gif",'ico',"border='0' align='absmiddle'",_T('spipbb:post_verifier_sujet'));
				echo "</a></div>\n";
				while ($row=sql_fetch($res2))
					{ echo "<input type='hidden' name='eraze[]' value='".$row['id_forum']."' />\n"; }
				echo "</td><td valign='absmiddle'></td></tr>\n";
				}
			else
				{
				while ($row=sql_fetch($res2))
					{
					echo "<tr class='verdana2' bgcolor='".$couleur."'><td>\n";
					echo "<div class='verdana2' style='margin-left:25px; padding:2px;'>\n";
					echo "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' align='absmiddle' />&nbsp; ".$row['id_forum']." - ".propre($row['titre']);
					echo "</div>\n";
					echo "</td><td valign='absmiddle'>\n";
					echo "<input type='checkbox' name='eraze[]' value='".$row['id_forum']."' />\n";
					echo "</td></tr>\n";
					}
				}
		}
	}

	echo "</table><br />\n";
	echo tout_de_selectionner("formeffacer");
	echo "<div align='right' class='verdana3'>\n"._T('spipbb:selection_efface').
		"\n<input type='submit' value='"._T('spipbb:effacer')."' class='fondo' />\n".
		"</div></form>\n";
	fin_cadre_relief();

	# pied page exec
	bouton_retour_haut();

	echo fin_gauche(), fin_page();
} // exec_spipbb_effacer
?>