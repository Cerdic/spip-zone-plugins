<?php
/*
+-------------------------------------------+
| Hugues AROUX - SCOTY @ koakidi.com
+-------------------------------------------+
| Page Effacer des posts
+-------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_spipbb_effacer() {


	# requis spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee,
			$spip_display;

	# initialiser spipbb
	include_spip('inc/spipbb_init');


	# requis de cet exec
	#


	#
	# affichage
	#
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_L('titre_page_'._request('exec')), "forum", "spipbb_admin",'');
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
		echo gros_titre(_T('gaf:poste_effac'),'',false);
		
		$nbr_eraze = count($tbl_eraze);
		if ($nbr_eraze==0) {
			echo "<div class='verdana3'><b>"._T('gaf:aucun_pt')."</b></div>";
		}
		else {
			foreach($tbl_eraze as $id)
				{ $req = spip_query("DELETE FROM spip_forum WHERE id_forum=$id and statut='off'"); }
			echo "<div class='verdana3'>".$nbr_eraze._T('gaf:poste_effac')."</div>";
		}
		fin_cadre_relief();
	}

	$res=spip_query("SELECT id_forum, id_parent, titre, id_thread, COUNT(id_forum) as total_post 
					FROM spip_forum WHERE statut = 'off' GROUP BY id_thread ");

	debut_cadre_relief("");
	echo "<form action='".generer_url_ecrire("spipnn_effacer")."' method='post'>\n";
	echo "<input type='hidden' name='action' value='efface_select'>\n";
	echo "<table cellpadding='3' cellspacing='0' border='0' width='100%'>\n";
	echo "<tr width='100%'>\n";
	echo "<td height='35' valign='top' colspan='2'>\n";
	echo gros_titre(_T('gaf:poste_refuse'),'',false);
	echo "</td></tr>";

	$ifond=0;
	
	while ($row=spip_fetch_array($res)) {
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
			http_img_pack("plus.gif",'ico',"align='absmiddle' border='0'",_T('gaf:voir')).
			"</a>";
		echo "</div>";
		echo "</td><td valign='absmiddle'>\n";
		echo "<input type='checkbox' name='eraze[]' value='".$id_post."'>";
		echo "</td></tr>\n";
		if ($total_post>'1') {
			$res2=sql_query("SELECT id_forum, titre FROM spip_forum 
							WHERE id_thread=$id_thread AND statut='off' AND id_forum!=$id_post");
			
			if($id_parent=='0')
				{
				$nbr_post=$total_post-1;
				echo "<tr class='verdana2' bgcolor='".$couleur."'><td>\n";
				echo "<div class='verdana2' style='color:#ED4242; margin-left:30px; padding:2px;'>\n";
				echo "<img src='"._DIR_IMG_SPIPBB."gaf_post-12.gif' align='absmiddle' />&nbsp;";
				echo _T('gaf:poste_efface_lui');
				echo "<a href='".generer_url_ecrire("spipbb_sujet","id_sujet=".$id_post)."'>&nbsp;".
						http_img_pack("plus.gif",'ico',"border='0' align='absmiddle'",_T('gaf:sujet_verifie'));
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
	echo "<div align='right' class='verdana3'>\n"._T('gaf:selection_efface').
		"\n<input type='submit' value='"._T('gaf:effacer')."' class='fondo' />\n".
		"</div></form>\n";
fin_cadre_relief();



# pied page exec
bouton_retour_haut();

echo fin_gauche(), fin_page();
} // exec_spipbb_effacer
?>
