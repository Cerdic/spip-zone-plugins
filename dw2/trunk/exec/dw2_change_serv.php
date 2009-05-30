<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifi� KOAK2.0 strict, mais si !
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
$lig = sql_fetsel("url, heberge, id_serveur","spip_dw2_doc","id_document='$id_doc'");
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
	
	//Donc ... d�tail du serveur enregistr� dans dw2, du Doc d'entr�e
	$ro = sql_fetsel("*","spip_dw2_serv_ftp","id_serv='$id_servdoc'");
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
		//  R�cup. date de derni�re modification
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
		//  R�cup. date de derni�re modification ; taille fichier
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
$row=sql_fetsel("*","spip_dw2_serv_ftp","id_serv=$id_serv");
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
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('dw:titre_page_deloc'), "suivi", "dw2_deloc");

	echo "<a name='haut_page'></a><br />";
echo gros_titre(_T('dw:titre_page_deloc'),'','',true);


echo debut_gauche('',true);
	// fonctions principales dw_deloc.php
	menu_administration_deloc();
	
	// module outils
	bloc_popup_outils();
	
	// retour dw2 admin
	bloc_ico_page(_T('dw:acc_dw2_st'), generer_url_ecrire("dw2_admin"), _DIR_IMG_DW2."telech.gif");
	echo "<br />\n";
	
	// Def. module doc deloc
	echo "<br />\n";
	echo debut_boite_info(true);
		echo "<span class='verdana2'>"._T('dw:txt_dd_intro_gauche')."</span><br />\n";
	echo fin_boite_info(true);
	
echo creer_colonne_droite('',true);

	// vers popup aide 
	bloc_ico_aide_ligne();

	// signature
	echo "<br />\n";
	echo debut_boite_info(true);
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	echo fin_boite_info(true);
	echo "<br />\n";

echo debut_droite('',true);


echo debut_cadre_relief(_DIR_IMG_DW2."list_serv.gif",true);

// titre
debut_band_titre($couleur_foncee, "verdana3", "center");
	echo _T('dw:changer_assoc_fichier_serveur')."\n";
fin_bloc();
		
debut_band_titre("#EFEFEF",'verdana3', 'center');		
	echo _T('dw:assoc_du_fichier_cat', array('nomfich' => $nomfich))."\n";
fin_bloc();

echo "<br />\n";
		
debut_boite_filet('a');
	echo "<span class='verdana3'>"._T('dw:fichier_declar_sur_serv')."<br />\n";
	echo "<b>".$serv_act."</b></span><br />\n";
	if($message_conex && $message_conex!="connect") {
		message_echec_connexion($message_conex);
	}
	echo "<span class='arial2'><b>"._T('dw:controle_sur_serv')."</b></span><br />\n ";
	if ($aff_no_doc)
		{ echo "<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif' valign='absmiddle' alt='' /> "._T('dw:exist_pas_sur_serv'); }
	else
		{ echo "<img src='"._DIR_IMG_DW2."puce-verte-breve.gif' valign='absmiddle' alt='' /><span class='verdana3'> $nomfich, $datemod - $taille - OK !.</span>\n"; }
			
	if ($statut=='archive')
		{ echo "<div style='background:#E8C8C8;'><span class='verdana3'>"._T('dw:fichier')." : "._T('dw:doc_dans_archive', array('nb_archive' => ''))."</span></div>\n"; }
fin_bloc();

echo "<br />\n";
		
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
		echo "<span class='verdana3'>"._T('dw:effacer_fichier_local')."<br />\n";
		echo "[ $chemin_url ]</span><br />\n";
		echo "<input type='radio' name='erazfichier' value='oui' /> "._T('dw:oui')."<br />\n";
		echo "<input type='radio' name='erazfichier' value='non' checked='checked' /> "._T('dw:non')."<br />\n";
		}
		
	echo "<div align='right'>\n";
	echo "<input type='hidden' name='id_serveur' value='".$id_serv."' />\n".
		"<input type='hidden' name='heberge' value='".$site_distant."' />\n".
		"<input type='hidden' name='url' value='/".$chem_distant.$nomfich."' />\n";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_import", "id_serv=".$id_serv)."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-changerassociation-".$id_doc)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	echo "<input type='submit' value='"._T('dw:associer')."' class='fondo' />\n";
	echo "</div></form>";
	fin_bloc();
fin_bloc();

echo "<br />\n";

echo fin_cadre_relief(true);

//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>\n";

	echo fin_gauche().fin_page();
} // fin exec_
?>