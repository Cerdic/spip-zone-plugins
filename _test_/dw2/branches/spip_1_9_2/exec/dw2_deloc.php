<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Gestion documents délocalisés
+--------------------------------------------+
| Documents Délocalisés
| * Insertion virtuelle dans SPIP et DW2
|   de Documents provenant de serveurs distants.
| * Export de Documents vers serveurs distants
+--------------------------------------------+
*/
 

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_deloc() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
$page_affiche=_request('exec');


//
// requis dw
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");
include_spip("inc/dw2_inc_deloc");

//
// prepa
//

$query = "SELECT * FROM spip_dw2_serv_ftp ORDER BY serv_ftp";
$result = spip_query($query);
$nbserv = spip_num_rows($result);

//
// affichage
//

debut_page(_T('dw:titre_page_deloc'), "suivi", "dw2_deloc");
	echo "<a name='haut_page'></a><br />";
gros_titre(_T('dw:titre_page_deloc'));


debut_gauche();
	// fonctions principales dw_deloc.php
	menu_administration_deloc();
	
	// module outils
	bloc_popup_outils();
	
	// retour dw2 admin
	bloc_ico_page(_T('dw:acc_dw2_st'), generer_url_ecrire("dw2_admin"), _DIR_IMG_DW2."telech.gif");
	echo "<br />";
	
	// Def. module doc deloc
	echo "<br />";
	debut_boite_info();
		echo "<span class='verdana2'>"._T('dw:txt_dd_intro_gauche')."</span><br />";
	fin_boite_info();
	
creer_colonne_droite();

	// vers popup aide 
	bloc_ico_aide_ligne();

	// signature
	echo "<br />";
	debut_boite_info();
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	fin_boite_info();
	echo "<br />";

debut_droite();


debut_cadre_relief("");


if ($nbserv == 0)
	{
	echo "<div align='center'>"._T('dw:aucun_serv_enreg')."</div><br />";
	}
else
	{
	// bloc info export .. bloc infos Import
	echo "<table width='100%' align='center' border='0' cellpadding='3' cellspacing='3'>\n";
	echo "<tr>";
	echo "<td valign='top' width='50%'>\n";
	debut_boite_filet('a','left');
		echo "<img src='"._DIR_IMG_DW2."export-24.gif' border='0'> "._T('dw:export')."<br />";
	fin_bloc();
	echo "</td><td valign='top' width='50%'>\n";
	debut_boite_filet('a','right');
		echo _T('dw:import_virtuel')." <img src='"._DIR_IMG_DW2."import-24.gif' border='0'><br />";
	fin_bloc();
	echo "</td></tr></table>";

	// selection du serveur
	debut_boite_filet('a','left');		
	$ifond = 0;
	echo "<table width='100%' cellpadding='4' cellspacing='0'>\n";

	while ($row=spip_fetch_array($result))
		{
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
		
		$id_serv = $row['id_serv'];
		$serv_ftp = $row['serv_ftp'];
		$host_dir = $row['host_dir'];
		$chem_dist = $row['chemin_distant'];
		$site_dist = $row['site_distant'];
		$designe = $row['designe'];
		if ($host_dir=='') {$host_dir = '/'; }
				
		echo "<tr bgcolor='$couleur'>";
		//bouton export
		echo
		"<td width='8%'><div align='center'>\n".
		"<a href='".generer_url_ecrire("dw2_export", "id_serv=".$id_serv)."'>\n".
		"<img src='"._DIR_IMG_DW2."export-24.gif' title='"._T('dw:export_vers_serv')."' alt='export' />\n".
		"</a></div></td>\n";
		
		// affichage ligne détail du serveur
		echo 
		"<td width='84%'>\n".
		"<div style='float:left; padding-right:5px;'>\n".
		"<a href='".generer_url_ecrire("dw2_serv_details", "id_serv=".$id_serv)."'>\n".
		"<img src='".aff_logo_serv($id_serv)."' title='"._T('dw:affiche_serveur')."' alt='logo' align='absmiddle' />\n".
		"</a></div>".
		"<a href='".generer_url_ecrire("dw2_serv_details", "id_serv=".$id_serv)."'>\n".
		"<span class='verdana3'><b>".$designe."</b></span></a><br />\n".
		"<span class='arial2'> ".$serv_ftp.$host_dir.$chem_dist."</span>".
		"</td>\n";

		// bouton d'import
		echo
		"<td width='8%'><div align='center'>\n".
		"<a href='".generer_url_ecrire("dw2_import", "id_serv=".$id_serv)."'>".
		"<img src='"._DIR_IMG_DW2."import-24.gif' title='"._T('dw:import_depuis_serv')."' alt='import'>".
		"</a></div></td>\n";
		
		echo "</tr>\n";
		}
		
		
	echo "</table>"; 
	fin_bloc();
	echo "<br />";
	}
	
fin_cadre_relief();


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
