<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Info du serveur + fichiers lies
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_serv_details() {

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

// reconstruire .. var=val des get et post
// var : id_serv
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

$id_serv=intval($id_serv);


$query = "SELECT id_serv, serv_ftp, host_dir, chemin_distant, site_distant, login, mot_passe, ".
			"DATE_FORMAT(date_crea,'%d/%m/%Y') AS datecrea, designe ".
			"FROM spip_dw2_serv_ftp WHERE id_serv=$id_serv";
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

debut_cadre_relief(_DIR_IMG_DW2."fich_serv.gif");


if ($nbserv==0)
	{
	echo "<br /><b>"._T('dw:aucun_serv_enreg')."<br />";
	echo "<form action='".generer_url_ecrire("dw2_serv_edit")."' method='post'>";
	echo "<div align='right' style='padding:5px;'>";
	echo _T('dw:saisir_nouv_serv_ftp');	
	echo "<input type=submit value='"._T('dw:suite')."' class='fondo'></div></form>";
	}
else
	{
	// titre
	debut_band_titre($couleur_foncee, "verdana3", "bold");
		echo _T('dw:serveur');
	fin_bloc();
	
	//
	// tableau 
	$ifond = 0;
	echo "<br />";
	echo "<table width='100%' border='0' cellpadding='2' cellspacing='0' class='verdana2'>\n";
	echo "<tr bgcolor='$couleur_foncee'>";
	echo "<td colspan='2'></td>\n";
	echo "<td width='35%'><div align='right'><font color='#FFFFFF'>"._T('dw:fonctions')."</font></div></td>\n";//
    echo "</tr>\n";
	
	$row=spip_fetch_array($result);

		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
		
		$id_serv = $row['id_serv'];
		$serv_ftp = $row['serv_ftp'];
		$host_dir = $row['host_dir'];
		$chem_distant = $row['chemin_distant'];
		$site_distant = $row['site_distant'];
		$login = $row['login'];
		$mot_passe = $row['mot_passe'];
		$designe = $row['designe'];
		$datecrea = $row['datecrea'];
		if ($host_dir=='') {$host_dir = '/'; }
		$pathroot = $serv_ftp.$host_dir.$chem_distant;
		
		// ligne du tableau
		
		echo "<tr bgcolor='$couleur'>";
		echo "<td width='3%'>$bouton</td>\n";
		echo "<td width='62%'>".
		"<div style='float:left; margin-right:5px;'><img src='".aff_logo_serv($id_serv)."' align='absmiddle'></div>".
		"<span class='verdana2'><b>".$designe."</b></span><br />\n".
		"<span class='arial2'>".$pathroot."</span></td>\n";
		echo "<td width='35%'>";
		
	
		
		// bouton "editer" 
		bloc_minibout_act(_T('dw:editer_serveur'), generer_url_ecrire("dw2_serv_edit", "id_serv=".$id_serv), _DIR_IMG_DW2."fich_serv.gif","","");

		// bouton "Dupliquer"
		bloc_minibout_act(_T('dw:affect_repert_hote'), generer_url_ecrire("dw2_serv_edit", "id_serv=".$id_serv."&duplic=oui"), _DIR_IMG_PACK."breve-24.gif","","");
		
		// vers page d'Import de ce serveur
		bloc_minibout_act(_T('dw:ouvr_page_import'), generer_url_ecrire("dw2_import", "id_serv=".$id_serv), _DIR_IMG_DW2."import-24.gif","","");
		//		
		
		echo "</td></tr>\n";
		
		echo "<tr><td colspan='3'>";

		conten_bloc_bout("right","25");
		$contenu = "<span class='verdana3'>".$id_serv."</span>";
		echo bouton_alpha($contenu);
		fin_bloc();

		echo _T('dw:lien_telecharg')." : ".$site_distant."/".$chem_distant."<br />\n";
		echo _T('dw:date_crea')." : ".$datecrea."<br />\n";
		
		
		// modif de l'intitule
		debut_boite_filet('b');
		echo "<form action='".generer_url_action("dw2actions", "arg=modifierintitule-".$id_serv)."' method='post' class='cadre-padding'>\n";
		echo "<table width='100%' celspacing='0' celpadding='0'><tr>";
		echo "<td width='25%'>"._T('dw:intitule_serveur')."</td>\n";
		echo "<td with='65%'><input type='text' name='designe' value='".$designe."' size='40' class='fondl' /></td>\n";
		echo "<td width='10%'>";
		echo "<input type='image' src='"._DIR_IMG_DW2."ok_fich.gif' align='absmiddle'>\n";
		echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_serv_details", "id_serv=".$id_serv)."' />\n";
		echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-modifierintitule-".$id_serv)."' />";
		echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
		echo "</td></tr></table></form>\n";
		fin_bloc();
		
		echo "</td></tr>";

	echo "</table>\n";


	//
	// affichage liste des fichiers affectés
	
		debut_cadre_relief(_DIR_IMG_PACK."doc-24.gif");
		echo "<div style='margin:2px; padding:3px;' align='center'>";

		$r_doc = spip_query("SELECT d.id_document, d.url, d.heberge, d.id_serveur, s.taille ".
							"FROM spip_dw2_doc AS d ".
							"LEFT JOIN spip_documents AS s ON d.id_document=s.id_document ".
							"WHERE id_serveur=$id_serv AND d.statut='actif' AND s.id_document IS NOT NULL");
		$listfich = array();
		while ($ldoc=spip_fetch_array($r_doc))
			{
			$numdoc = $ldoc['id_document'];
			$url = $ldoc['url'];
			$heberge = $ldoc['heberge'];
			$id_serv = $ldoc['id_serveur'];
			$taille_f = $ldoc['taille'];
			$nomfichier = substr(strrchr($url,'/'), 1); // extrait nomfichier d'url
			
			$info_s = taille_octets($taille_f);
			$listfich[$nomfichier] = $info_s;
			}
		//prepa param double_colonne
		reset ($listfich);
		$nb_doclie = count($listfich);
				
		$chaine_titre = "doc_assoc";
		$icone_item ="puce-verte-breve.gif";
		$info_supp ="info";
		
		//tableau 2 colonnes
		double_colonne($listfich, $chaine_titre, $icone_item, $info_supp);

		fin_cadre_relief();
		
		
		//  si aucun doc lié .. bouton "effacer"
		if ($nb_doclie==0) {
			echo "<div style='float:right; text-align:center;' title='"._T('dw:affacer_serv')."'>\n";
			echo "<a>";
			echo "<form action='".generer_url_action("dw2actions", "arg=effaceserveur-".$id_serv)."' method='post'>\n";
			echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_deloc")."' />\n";
			echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-effaceserveur-".$id_serv)."' />\n";
			echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";

			echo "<input type='image' src='"._DIR_IMG_PACK."poubelle.gif'>\n";
			
			#bloc_minibout_act(_T('dw:affacer_serv'), , _DIR_IMG_PACK."poubelle.gif","","");
			echo "</form>\n";
			echo "</a></div>\n";
			echo "<div style='clear:both;'></div>\n";
		}
		
	}
fin_cadre_relief();


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
