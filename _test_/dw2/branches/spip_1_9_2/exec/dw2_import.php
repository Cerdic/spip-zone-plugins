<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Liste fichiers d'un "serveur" et leur affectations
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_import() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
#$page_affiche=_request('exec');

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

// reconstruire .. var=val des get et post
// var : id_serv
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

$id_serv=intval($id_serv);



// details du serveur de destination : mon_site_ftp
$query ="SELECT * FROM spip_dw2_serv_ftp WHERE id_serv='$id_serv'";
$result= spip_query($query);
$row = spip_fetch_array($result);
	$ftp_server = $row['serv_ftp'];				// ftp.machin.net
	$port = $row['port'];
	$ftp_user_name = $row['login'];	
	$ftp_user_pass = $row['mot_passe'];
	$site_distant = $row['site_distant'];			// http://www.machin.net
	$host_dir = $row['host_dir'];					//  /host_dir/   ou vide
	if ($host_dir=='') { $host_dir='/'; }								
	$repert_distant = $row['chemin_distant'];		//  doss1/doss2/
	$repertoire_dest = $host_dir.$repert_distant;	//  /host_dir/doss1/doss2/


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
//
// tableau

debut_cadre_relief(_DIR_IMG_DW2."import-24.gif");


// titre
debut_band_titre($couleur_foncee, "verdana3", "center");
	echo _T('dw:import')."<br />";
	echo _T('dw:ouvrir_serv').$ftp_server.$repertoire_dest."<br />\n";
fin_bloc();


	
//
// connexion au serveur

$retour_conex = connexion_serv($ftp_server, $port, $ftp_user_name, $ftp_user_pass, $repertoire_dest);
$conex=$retour_conex[0];
$message_conex=$retour_conex[1];

if($conex) {
	
	// Recup. listing des fichiers contenu dans le repertoire
	// h.27/01/06 tester avec rawlist si nlist : out ? .. non !
	$listage = ftp_nlist($conex, "");
	if (empty($listage)) {
		debut_band_titre('');
		echo "<div class='bandeau-principal' style='padding:5px;'><b>".
			$ftp_server." -> ".$repertoire_dest._T('dw:serv_sans_fich')."</b></div><br />\n";
		echo "<br />"._T('dw:select_autre_serv')."<br />\n";
		fin_bloc();
		@ftp_quit($conex);		
		break;
	}
	else {
		foreach ($listage as $item) { 
			if (ereg("\.([^.]+)$", $item))
				{ $listfich[$item] = ftp_size($conex, $item); }
		}
	}
	@ftp_quit($conex);

//
// prepa tableau
//
	reset ($listfich);
	
	// Separer le tableau sur 2 col.
	ksort($listfich);
	$nb_k = count($listfich);
	$af = ceil($nb_k/2); 
	$a_list = array_slice($listfich, '0', $af);
	$b_list = array_slice($listfich, $af);  //@array_slice
	$double_colonne = array($a_list, $b_list);
	
	// recup tableau du Catalogue DW2
	$fich_q = "SELECT id_document, SUBSTRING_INDEX(url, '/', -1) AS idmfich FROM spip_dw2_doc";
	$fich_r = spip_query($fich_q);

	while($fich_t = spip_fetch_array($fich_r)) {
		$tab_t[$fich_t['id_document']]=$fich_t['idmfich'];
	}
	
	reset ($tab_t);

	echo "<br />";
	debut_band_titre("#DFDFDF");
	echo "<span class='verdana3'><b>";
	if($nb_k<=1)
		{ echo _T('dw:contient_nb_fich', array('nb_k' => $nb_k)); }
	else
		{ echo _T('dw:contient_nb_fich_s', array('nb_k' => $nb_k)); }
	echo "</b></span>\n";
	fin_bloc();
	
	echo "<table width='100%' border='0' cellpadding='2' cellspacing='0' align='center'><tr>\n";

	//
	// Afficher les 2 colonnes (a_list - b_list)
	$ic='0';
	foreach($double_colonne as $aff_colonne) {
		echo "<td width='50%' valign='top'>";
		echo "<table width='100%' border='0' cellpadding='1' cellspacing='0' align='center'>";
		
		$ifond = $ic;
		while (list($fichier, $taille) = each($aff_colonne))
			{
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
			
			// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 20 caract
			$fichier_aff = wordwrap($fichier,20,' ',1);
		
			// si fichier dans catalogue DW2 (array $tab_t) on embarque id_document pour bout_select_fichier
			if(in_array($fichier,$tab_t))
				{
				$idmf = '1';
				$iddoc = array_search($fichier,$tab_t);
				$r_etat=spip_query("SELECT statut FROM spip_dw2_doc WHERE id_document=$iddoc");
				$ligne=spip_fetch_array($r_etat);
				$etat_arch=$ligne['statut'];
				// on distingue les doc en archive
				if ($etat_arch=='archive') {$couleur="#E8C8C8"; }
				}
			else
				{ $idmf='0'; }
	
			echo "<tr bgcolor='".$couleur."'>";
			echo "<form action='".generer_url_action("dw2actions", "arg=inclusdocserveur-".$id_serv)."' method='post'>";
			echo "<td width='7%' height='25'>".
				"<input type='hidden' name='id_serv' value='".$id_serv."'>\n".
				"<input type='hidden' name='fichier' value='".$fichier."'>\n".
				"<input type='hidden' name='taille' value='".$taille."'>\n".
				"<input type='hidden' name='repert_dest' value='".$repert_distant."'>\n".
				"<input type='hidden' name='sitedist' value='".$site_distant."'>\n";
				
			echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_affect_doc")."' />\n";
			echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-inclusdocserveur-".$id_serv)."' />";
			echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";

			echo bout_select_fichier($fichier, $idmf, $iddoc, $id_serv, $site_distant, $repert_distant)."</td>\n".
				"<td><span class='verdana2'>".$fichier_aff."</span></td>\n";
			echo "<td width='28%'><div align='right'><span class='verdana2'>".taille_octets($taille)."</span></div></td>";
			echo "</form></tr>";
			}
		echo "</table></td>";
		$ic++;
	}
	
	echo "</tr></table><br />";
}
else {
	message_echec_connexion($message_conex);
}
fin_cadre_relief();


//
// notice  des icones
//
debut_cadre_relief("");
	echo "<table width='100%' border='0' cellpadding='2' cellspacing='0' class='verdana2'>";
	echo "<tr><td width='5%'><img src='"._DIR_IMG_DW2."puce-verte.gif' align='absmiddle'>";
	echo "</td><td>"._T('dw:txt_defico_import_1')."</td></tr>";
	//echo "<img src='"._DIR_IMG_PACK."puce-rouge-breve.gif' align='absmiddle'>&nbsp;"._T('dw:txt_defico_import_2')."<br />";
	echo "<tr><td width='5%'><img src='"._DIR_IMG_DW2."puce-poubelle-breve.gif' align='absmiddle'>";
	echo "</td><td>"._T('dw:txt_defico_import_3')."</td></tr>";
	echo "<tr><td width='5%'> </td><td><br /><b>"._T('dw:txt_defico_import_4')."</b></td></tr>";
	echo "<tr><td width='5%'><img src='"._DIR_IMG_DW2."dot_serveur2.gif' align='absmiddle' border='0'>";
	echo "</td><td>"._T('dw:txt_defico_import_5')."</td></tr>";
	echo "<tr><td width='5%'><img src='"._DIR_IMG_DW2."dot_serveur.gif' align='absmiddle' border='0'>";
	echo "</td><td>"._T('dw:txt_defico_import_6')."</td></tr>";
	echo "<tr><td width='5%'><img src='"._DIR_IMG_DW2."dot_serveur3.gif' align='absmiddle'>";
	echo "</td><td>"._T('dw:txt_defico_import_7')."</td></tr>";
	echo "<tr><td width='5%'><img src='"._DIR_IMG_DW2."puce-blanche-breve.gif' align='absmiddle'>";
	echo "</td><td>"._T('dw:txt_defico_import_8').$ftp_server.$repertoire_dest."</td></tr>";
	echo "<tr><td width='5%'> </td><td> </td></tr>";
	echo "<tr><td width='5%'><img src='"._DIR_IMG_PACK."attachment.gif' align='absmiddle'>";
	echo "</td><td>"._T('dw:txt_defico_import_10')."</td></tr>";
	echo "<tr><td width='5%'><div style='display:inline; width:14px; background:#E8C8C8;'>&nbsp;&nbsp;&nbsp;</div>";
	echo "</td><td>"._T('dw:txt_defico_import_9')."</td></tr>";
	echo "</table>";
fin_cadre_relief();


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
