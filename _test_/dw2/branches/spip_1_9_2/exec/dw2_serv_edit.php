<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| .Formulaire serveur - nouveau
| .Modifir paramètres de serveur ftp
| .Mode 'duplication' ..
|  seul champ 'repertoire' modifiable !
+--------------------------------------------+
*/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_serv_edit() {

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
// var : id_serv ($duplic) , $retour
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


//
// traitement retour
$mess_err=array();

if($retour) {
	$tab_retour=explode(",",$retour);
	// donnees OK ! 
	if(!is_numeric($tab_retour[0])) {
		if ($tab_retour[0]=="connect") { $aff_final=true; }
		else { $mess_err[]=$tab_retour[0]; }
	} 
	// prepa message en tete de page
	else {
		foreach($tab_retour as $k) {
			$mess_err[]=$k;
		}
	}
}

if(isset($id_serv)) {
	$id_serv=intval($id_serv);
	
	// cas modif / Duplication serveur
	$q_serv = "SELECT * FROM spip_dw2_serv_ftp WHERE id_serv='$id_serv'";
	$r_serv = spip_query($q_serv);
	$rw = spip_fetch_array($r_serv);
	$serv_ftp = $rw['serv_ftp'];
	$host_dir = $rw['host_dir'];
	$port = $rw['port'];
	$chemin_distant = $rw['chemin_distant'];
	$site_distant = $rw['site_distant'];
	$login = $rw['login'];
	$mot_passe = $rw['mot_passe'];
	$designe = $rw['designe'];
	$datecrea = $rw['datecrea'];
	
	// si duplication .. champs bloques
	if($duplic=='oui') {
		$readonly = "readonly='readonly'";
		$titre_form = _T('dw:duplic_serv');
		$texte_intro = _T('dw:txt_duplic_serv');
	}
	else {
		$titre_form = _T('dw:modif_serv')."<br />".$serv_ftp." - ".$designe;
		$texte_intro = _T('dw:txt_nouv_serveur');
	}

}
else {
	// cas nouveau serveur
	$titre_form = _T('dw:nouveau_serveur');
	$texte_intro = _T('dw:txt_nouv_serveur');
}

// particularite
if(empty($port)) { $port = '21'; }

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

	// rappel Serveurs
	# .. faire un fichier à inclure
	$rq_serv = spip_query("SELECT * FROM spip_dw2_serv_ftp");
	if(spip_num_rows($rq_serv)) {
		debut_boite_filet("a");
		echo "<table width='100%' cellpadding='2' cellspacing='0'>";
		$ifond = 0;
		while ($lgs = spip_fetch_array($rq_serv))
			{
			$numserv = $lgs['id_serv'];
			$servftp = $lgs['serv_ftp'];
			$designe = $lgs['designe'];

			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
			
			echo "<tr bgcolor='$couleur'><td colspan='3'><span class='verdana2 bold'>".$servftp."</span></td></tr>";
			
			echo "<tr bgcolor='$couleur'><td width='20%'>";
			// bouton "editer" 
			bloc_minibout_act(_T('dw:editer_serveur'), generer_url_ecrire("dw2_serv_edit", "id_serv=".$numserv), _DIR_IMG_DW2."fich_serv.gif","","");
			echo "</td><td>";
			echo "<span class='verdana2'>".$designe."</span>";
			echo "</td><td width='20%'>";
			// bouton "Dupliquer"
			bloc_minibout_act(_T('dw:affect_repert_hote'), generer_url_ecrire("dw2_serv_edit", "id_serv=".$numserv."&duplic=oui"), _DIR_IMG_PACK."breve-24.gif","","");
			echo "</td></tr>";
			}
		echo "</table>";
		fin_bloc();
	}

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

	// titre du formulaire
	debut_band_titre($couleur_foncee, 'verdana3', 'center');
	echo "<div align='center'>";
	echo "<b>".$titre_form."</b></div>";
	fin_bloc();
	
	// text intro 
	$invisible = $id_serv;
	debut_boite_filet('a','left');
	if ($invisible)
		echo bouton_block_invisible('mess_alert');
	else 
		echo bouton_block_visible('mess_alert');
	echo "<span class='verdana3'> <b> [ "._T('dw:attention_info')." ]</b></span>";
	
	if ($invisible)
		echo debut_block_invisible('mess_alert');
	else
		echo debut_block_visible('mess_alert');
	echo "<span class='verdana2'>".$texte_intro."</span><br />\n";
	fin_bloc();
	echo fin_block();


	//
	// en retour si erreur ... affiche :
	foreach($mess_err as $v) {
		debut_cadre_relief(_DIR_IMG_PACK."warning-24.gif");
		echo "\n<span style='color:red;'>"._T('dw:mess_err_'.$v)."</span><br />\n";
		fin_cadre_relief();
	}

	//
	// formulaire
	//
	debut_band_titre("");

	echo "\n<form action='".generer_url_action("dw2actions", "arg=serveredit-rien")."' method='post'>\n";
	
	echo "<table width='100%' border='0' cellpadding='2' cellspacing='0'>\n";
	
		tr_tab_nouv(_T('dw:hote_ftp'), 'text', 'serv_ftp', $serv_ftp, _T('dw:serv_info_hote'), $readonly);
		tr_tab_nouv(_T('dw:repert_hote'), 'text', 'host_dir', $host_dir, _T('dw:serv_info_hostdir'), $readonly, false);
		tr_tab_nouv(_T('dw:repertoire_s'), 'text', 'chemin_distant', $chemin_distant, _T('dw:serv_info_chemdist'), '', false);
		tr_tab_nouv(_T('dw:port'), 'text', 'port', $port, _T('dw:serv_info_port'), $readonly);
		tr_tab_nouv(_T('dw:login'), 'text', 'login', $login, '', $readonly, false);
		tr_tab_nouv(_T('dw:mot_passe'), 'password', 'mot_passe', $mot_passe, '', $readonly, false);
		tr_tab_nouv(_T('dw:url_site'), 'text', 'site_distant', $site_distant, _T('dw:serv_info_sitedist'), $readonly);

	echo "<tr><td colspan='3'><div align='right'>\n";
	// fin tableau
	
	if ($duplic=='oui') {
		echo "<input type='hidden' name='duplic' value='oui'>";
	}

	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_serv_edit")."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-serveredit-rien")."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";

	echo "<input type='hidden' name='id_serv' value='$id_serv'>\n";
	if(!$aff_final) {
		echo "<input type='submit' value='".
			$afbout = ($duplic=='oui') ? _T('dw:dupliquer') : _T('dw:soumettre');
		echo "' class='fondo' />\n";
	}
	echo "</div></td></tr></table>\n";
	echo "</form>\n";
	fin_bloc();
	
	

// dernier affichage succes connexion a nouveau serveur
	if ($aff_final) {
		debut_band_titre("#DFDFDF");
		echo "<div align='center'>";
		echo "<b>"._T('dw:acces_serv_enreg')."</b></div>\n";
		fin_bloc();
	}


fin_cadre_relief();


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_

?>
