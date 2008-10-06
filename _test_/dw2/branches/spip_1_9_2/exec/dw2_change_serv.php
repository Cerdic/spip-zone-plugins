<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Traitement doublons fichiers
| Serveur <-> local
| Serveur 1 <-> Serveur 2
+--------------------------------------------+
*/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_change_serv() {

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
// var : $id_doc, $id_serv
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

$id_doc=intval($id_doc);
$id_serv=intval($id_serv);


//
// Fiche du doc/fichier dans catalogue
//
$reqdoc = spip_query("SELECT url, heberge, id_serveur FROM spip_dw2_doc WHERE id_document='$id_doc'");
$lig = spip_fetch_array($reqdoc);
$urldoc = $lig['url'];
$hebergedoc = $lig['heberge'];
$id_servdoc = $lig['id_serveur'];
$statut = $lig['statut'];
$nomfich = substr(strrchr($urldoc,'/'), 1); // extrait nomfichier de url
$chfich = str_replace($nomfich, '', $urldoc); // extrait repertoires de url
$chemfich = substr(strchr($chfich,'/'), 1); // vire le premier '/'


//
// controle 
//
if ($hebergedoc!='local' && $hebergedoc!='distant') {
	
	//Donc ... détail du serveur enregistré dans dw2, du Doc d'entrée
	$quero ="SELECT * FROM spip_dw2_serv_ftp WHERE id_serv='$id_servdoc'";
	$reso = spip_query($quero);
	$ro = spip_fetch_array($reso);
	$ftp_server = $ro['serv_ftp'];
	$port = $ro['port'];						// ftp.machin.net
	$ftp_user_name = $ro['login'];	
	$ftp_user_pass = $ro['mot_passe'];
	$host_diro = $ro['host_dir'];						//  /host_dir/   ou vide
	if ($host_diro=='') { $host_diro='/'; }													
	$repert_distanto = $ro['chemin_distant'];			//  doss1/doss2/
	$repertoire_desto = $host_diro.$repert_distanto;	//  /host_dir/doss1/doss2/

	// connexion
	$retour_conex = connexion_serv($ftp_server, $port, $ftp_user_name, $ftp_user_pass, $repertoire_desto);
	$conex=$retour_conex[0];
	$message_conex=$retour_conex[1];
	
	if($conex) {
		//  Récup. date de dernière modification
		$datemodif = ftp_mdtm($conex, $nomfich);
		// recup. taille fichier
		$valtail = ftp_size($conex, $nomfich);		
		// fermer conex
		ftp_quit($conex);
				
		//traitement
		if ($datemodif != -1)
			{ $datemod = date("d/m/Y H:i", $datemodif); }
		else
			{ $datemod = 0; }
					
		if ($valtail != -1)
			{ $taille = taille_octets($valtail); }
		else
			{ $taille = '0'; }
							
		if ($taille==0 && $datemod==0)
			{ $aff_no_doc = "1"; }
	}
	else {
		$aff_no_doc = "1";
	}			
	$serv_act = $ftp_server.$repertoire_desto;
	
}
elseif ($hebergedoc=='local') {
		
	// donc .. pointer le fichier en Local
	$chemin_url = "..".$urldoc; // chemin complet local
	if (file_exists($chemin_url)) {
		//  Récup. date de dernière modification ; taille fichier
		$datemod = date ("d/m/Y H:i", filemtime($chemin_url));
		$taille = taille_octets(filesize($chemin_url));
	}
	else {
		$aff_no_doc = "1";
	}
	
	$serv_act = $hebergedoc." : ".$urldoc;
}


//
// detail serveur selectionne
//
$reserv=spip_query("SELECT * FROM spip_dw2_serv_ftp WHERE id_serv=$id_serv");
$row=spip_fetch_array($reserv);
$serv_ftp = $row['serv_ftp'];
$host_dir = $row['host_dir'];
$chem_distant = $row['chemin_distant'];
$site_distant = $row['site_distant'];
$designe = $row['designe'];
if ($host_dir=='') { $host_dir = '/'; }
$pathroot = $serv_ftp.$host_dir.$chem_distant;



//
// ... Affichage ...
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


debut_cadre_relief(_DIR_IMG_DW2."list_serv.gif");

// titre
debut_band_titre($couleur_foncee, "verdana3", "center");
	echo _T('dw:changer_assoc_fichier_serveur')."\n";
fin_bloc();
		
debut_band_titre("#EFEFEF",'verdana3', 'center');		
	echo _T('dw:assoc_du_fichier_cat', array('nomfich' => $nomfich))."\n";
fin_bloc();

echo "<br />";
		
debut_boite_filet('a');
	echo "<span class='verdana3'>"._T('dw:fichier_declar_sur_serv')."<br />";
	echo "<b>".$serv_act."</b></span><br />\n";
	if($message_conex && $message_conex!="connect") {
		message_echec_connexion($message_conex);
	}
	echo "<span class='arial2'><b>"._T('dw:controle_sur_serv')."</b></span><br />\n ";
	if ($aff_no_doc)
		{ echo "<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif' valign='absmiddle'> "._T('dw:exist_pas_sur_serv'); }
	else
		{ echo "<img src='"._DIR_IMG_DW2."puce-verte-breve.gif' valign='absmiddle'><span class='verdana3'> $nomfich, $datemod - $taille - OK !.</span>"; }
			
	if ($statut=='archive')
		{ echo "<div style='background:#E8C8C8;'><span class='verdana3'>"._T('dw:fichier')." : "._T('dw:doc_dans_archive', array('nb_archive' => ''))."</span></div>"; }
fin_bloc();

echo "<br />";
		
//
// formulaire nouvelle association fichier-serveur
debut_boite_filet('a');
	echo _T('dw:fichier_sur_serv_courant')."<br />\n";
	echo "<b>".$pathroot."</b><br /><br />\n";
	
	debut_band_titre($couleur_claire);	
	echo "<form action='".generer_url_action("dw2actions", "arg=changerassociation-".$id_doc)."' method='post'>\n";
	echo "<span class='verdana3'>"._T('dw:modif_assoc_vers_serv')."</span><br />\n";
		
	// propose de supprimer le fichier en local
	if ($hebergedoc=='local')
		{
		echo "<span class='verdana3'>"._T('dw:effacer_fichier_local')."<br />";
		echo "[ $chemin_url ]</span><br />";
		echo "<input type='radio' name='erazfichier' value='oui' /> "._T('dw:oui')."<br />";
		echo "<input type='radio' name='erazfichier' value='non' checked='checked' /> "._T('dw:non')."<br />";
		}
		
	echo "<div align='right'>";
	echo "<input type='hidden' name='id_serveur' value='".$id_serv."'>\n".
		"<input type='hidden' name='heberge' value='".$site_distant."'>\n".
		"<input type='hidden' name='url' value='/".$chem_distant.$nomfich."'>\n";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_import", "id_serv=".$id_serv)."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-changerassociation-".$id_doc)."' />";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
	echo "<input type='submit' value='"._T('dw:associer')."' class='fondo'>";
	echo "</div></form>";
	fin_bloc();
fin_bloc();

echo "<br />";


fin_cadre_relief();


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
