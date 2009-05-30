<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Catalogue des Docs exportables
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_export() {

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
// config 
$nbr_lignes_tableau=$GLOBALS['dw2_param']['nbr_lignes_tableau'];


// reconstruire .. var=val des get et post
// var : vl ; odb ; wltt
//		id_serv (exporterdoc, id_de
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

$id_serv=intval($id_serv);


//
// le serveur
//
$qs = spip_query("SELECT * FROM spip_dw2_serv_ftp WHERE id_serv=$id_serv");
$row = spip_fetch_array($qs);
	$ftp_server = $row['serv_ftp'];				// ftp.machin.net
	$port = $row['port'];
	$ftp_user_name = $row['login'];
	$ftp_user_pass = $row['mot_passe'];
	$site_distant = $row['site_distant'];			// http://www.machin.net
	$host_dir = $row['host_dir'];					//  /host_dir/   ou vide								
	$repert_distant = $row['chemin_distant'];		//  doss1/doss2/
	$repertoire_dest = $host_dir.$repert_distant;	//  /host_dir/doss1/doss2/


//
// EXPORTER
//
# laisser ici because blème (ftp_put) lorsque en "action" h.18/8/06
if($exporterdoc=='oui' && isset($id_de)) {
	// infos fichier a exporter
	$qdoc = "SELECT url FROM spip_dw2_doc WHERE id_document = $id_de";
	$rdoc = spip_query($qdoc);
	$ldoc = spip_fetch_array($rdoc);
		$url = $ldoc['url'];						// -> (dw2) /IMG/zip/monfichier.zip
		$fichier = substr(strrchr($url,'/'), 1);	// -> monfichier.zip
		$chemin_loc = "..".$url;
	

	// connexion et transfert fichier
	// 
	$retour_conex = connexion_serv($ftp_server, $port, $ftp_user_name, $ftp_user_pass, $repertoire_dest);
	$conex=$retour_conex[0]; // retour_conex => array a 2 val !
	$message_conex=$retour_conex[1];
	
	if($conex) {
		// transfert du fichier
		$upload = ftp_put($conex, $fichier, $chemin_loc, FTP_BINARY);
		if ($upload) {
			spip_log("dw_export_doc: $fichier - dest: $ftp_server $repertoire_dest");
			//si la copie réussit, on l'efface du repertoire local
			unlink($chemin_loc);
			//on met à jour dw2_doc
			$new_url_dw = "/".$repert_distant.$fichier;
			spip_query("UPDATE spip_dw2_doc SET id_serveur = '$id_serv', heberge = '$site_distant', ".
						"url = '$new_url_dw' WHERE id_document='$id_de'");
		}
		else {
			$message_conex = "echecupload";
		}
	}
	// on ferme boutique !
	ftp_quit($conex);
}




// Recup nombre de ligne passe en url, fixe debut LIMIT ...		
$dl=($vl+0);



//Nbr Total de Doc exportables
$rqndoc=spip_query("SELECT SUBSTRING_INDEX(url, '/', -1) AS fichier, id_document 
					FROM spip_dw2_doc 
					WHERE heberge='local' AND statut='actif' ORDER BY nom");
$nligne=spip_num_rows($rqndoc);
	
	
// prepa toutdeplier toutreplier + tableau des prem lettres
$gen_ltt = array();
while ($row_dep=spip_fetch_array($rqndoc)) {
	$iddoc=$row_dep['id_document'];
	$les_docs[] = "bout$iddoc";
	$nom_block = "bout$iddoc";
	if (!$numero_block["$nom_block"] > 0) {
		$compteur_block++;
		$numero_block["$nom_block"] = $compteur_block;
			if (!$first_couche) 
			{ $first_couche = $compteur_block; }
			{ $last_couche = $compteur_block; }
	}
	// tableau de toutes premieres lettres
	$gen_ltt[] = strtoupper(substr($row_dep['fichier'],0,1));
}
	
// gen_ltt => elimine doublons => tbl_ltt
reset ($gen_ltt);
$nbr_ltt=0;
while (list(,$ltt)=each($gen_ltt)) {
	if($ltt != $ltt_prec)
		{ $tbl_ltt[$ltt] = 1; }
	else
		{ $tbl_ltt[$ltt]++; }
	$ltt_prec = $ltt;
}

		
// Tri tableau : par date_crea ou par fichier
if ($odb=='date') { $orderby = 'd.date_crea DESC'; }
else { $orderby = 'fichier'; $odb='fichier'; }


// si tri alphabet'
if (isset($wltt)) {
	$where_ltt = "AND UPPER(SUBSTRING_INDEX(d.url, '/', -1)) LIKE '$wltt%'";
	// on redéfinis $nligne pour la function tranche
	reset($tbl_ltt);
	$nligne = $tbl_ltt[$wltt];
}


//
// requete principale du catalogue
//
$quer="SELECT d.id_document, DATE_FORMAT(d.date_crea,'%d/%m/%Y') AS datecrea, ".
		"SUBSTRING_INDEX(d.url, '/', -1) AS fichier, d.url, s.taille ".
		"FROM spip_dw2_doc AS d LEFT JOIN spip_documents AS s ON d.id_document=s.id_document ".
		"WHERE d.heberge = 'local' AND d.statut='actif' ".$where_ltt." ".
		"ORDER BY $orderby LIMIT $dl,$nbr_lignes_tableau";
		
$result=spip_query($quer);
$nbliens=spip_num_rows($result);


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


debut_cadre_relief(_DIR_IMG_DW2."export-24.gif");

// titre
debut_band_titre($couleur_foncee, "verdana3", "center");
	echo _T('dw:export')." "._T('dw:vers_serveur')."<br />";
	echo $ftp_server;
	if ($host_dir=='') { echo "/"; }
	echo $repertoire_dest;
fin_bloc();


	// si tentative d'export echouee : affiche ...
	if($message_conex && $message_conex!='connect') {
		echo "<br />";
		message_echec_connexion($message_conex);
	}
	// export ok !
	if($upload) {
		debut_boite_filet('a','center');
		echo "<img src='"._DIR_IMG_DW2."puce-verte-breve.gif' align='absmiddle' />&nbsp;";
		echo "<span class='verdana2'>"._T('dw:mess_export_fichier', array('fichier' => $fichier))."</span>";
		fin_bloc();
	}




if ($nbliens==0) {
		echo "<br /><b>"._T('dw:aucun_fichier_export')."</b><br />";

} else {
	// valeur de tranche affichee	
	$nba1 = $dl+1;

	
	debut_band_titre($couleur_claire, "verdana3", "bold");
	if(isset($wltt))
		{ echo "[ ".$wltt."... ]"; }
	
	if($nligne<=1)
		{ echo _T('dw:document_export', array('nb_doc' => $nligne)); }
	else
		{ echo _T('dw:document_export_s', array('nb_doc' => $nligne)); }
	fin_bloc();
	

	// affichage tranches
	debut_band_titre("#dfdfdf");
	tranches($nba1, $nligne, $nbr_lignes_tableau);
	fin_bloc();


	// affichage lettres pour tri-alphabetique
	bouton_tout_catalogue($page_affiche,"id_serv=".$id_serv);
	reset ($tbl_ltt);
	while (list($k,$v) = each($tbl_ltt))
		{
		echo "<a href='".generer_url_ecrire("dw2_export","id_serv=".$id_serv."&wltt=".$k)."' title='"._T('dw:document_s')." : $v'>\n";
		echo bouton_alpha($k);
		echo "</a>\n";
		}
	echo "<div style='clear:both;'></div>\n";	
	// 
	
	$ifond = 0;
	
	// Entete tableau ..
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%'>\n	
		<tr>\n";
	
	echo "<td width='60%' class='tete_colonne'>\n";
	if($odb!='fichier') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','fichier');
		echo "<a href='".$lien."'>"._T('dw:fichier')."</a>";
	} else {
		echo "<b>"._T('dw:fichier')."</b>";
	}
	echo "</td><td width='18%' class='tete_colonne'>\n";
	if($odb!='date') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','date');
		echo "<a href='".$lien."'>"._T('dw:entree_cat')."</a>";
	} else {
		echo "<b>"._T('dw:entree_cat')."</b>";
	}
	echo "</td><td width='14%' class='tete_colonne'>\n";
	echo _T('dw:taille');
	echo "</td><td width='8%' class='tete_colonne'>\n";
	echo _T('dw:exporter');
	echo "</td></tr>";


	while ($a_row=spip_fetch_array($result))
		{
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
		
		$iddoc = $a_row['id_document'];
		$nomfichier = $a_row['fichier'];
		$url = $a_row['url'];
		$cheminfichier = str_replace($nomfichier, '', $url); // extrait repertoires de url
		$datecrea = $a_row['datecrea'];
		$t_s=$a_row['taille'];
		
		if (!$t_s)
			{ $taille = "<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif' title='"._T('dw:pas_dans_spip')."' />"; }
		else
			{ $taille = taille_en_octets($t_s); }

		// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 30 caract
		$nomfichier = wordwrap($nomfichier,30,' ',1);

		// ligne du tableau
		//
		echo "<tr bgcolor='$couleur'>";
		echo "<td width='60%'><div class='verdana2'>";
		if (!$t_s)
			{ echo "<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif' title='"._T('dw:pas_dans_spip')."' />"; }
		echo "&nbsp;".$nomfichier."</div></td>\n";
		echo "<td width='18%'><div align='center' class='arial2'>".$datecrea."</div></td>\n";
		echo "<td width='14%'><div align='center' class='arial2'>".$taille."</div></td>\n";
		echo "<td width='8%'><div align='center' class='arial2'>\n";
		
		// bouton export
		echo "<form action='".generer_url_ecrire("dw2_export", "id_serv=".$id_serv)."' method='post' />\n";
		echo "<input type='hidden' name='exporterdoc' value='oui' />\n";
		echo "<input type='hidden' name='id_de' value='".$iddoc."' />\n";
		echo "<input type='image' src='"._DIR_IMG_DW2."ok_fich.gif' title='"._T('dw:exporter')."'>\n";
		echo "</form>";
		
		echo "</div></td>\n";
    	echo "</tr>\n";

		}
	echo "</table>";
}
fin_cadre_relief();


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
