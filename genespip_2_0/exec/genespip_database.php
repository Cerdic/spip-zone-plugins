
<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

    include_spip('inc/presentation');
    include_spip('inc/gedcom_fonctions_export');

function exec_genespip_database() {
	global $connect_statut, $connect_toutes_rubriques;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques))	{
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_page(true);
		exit;
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genespip:genealogie'), "naviguer", "genealogie");

	echo debut_gauche('',true);
	
	echo debut_boite_info(true);
	echo propre(_T('genespip:info_doc'));
	echo fin_boite_info(true);
	
	$rac=icone_horizontale(_T('genespip:liste_patronyme'), generer_url_ecrire("genespip"), '../'._DIR_PLUGIN_GENESPIP.'/img_pack/globe.gif', '',false);
	echo bloc_des_raccourcis($rac);
	
	echo debut_droite('',true);
	
	echo debut_cadre_relief();
	echo gros_titre(_T('genespip:gedcom'), '', false);
	echo "<br />";
	if ($_POST['action']=='gedcom'){
		include_spip('inc/gedcom_fonctions');
		$date_upload=date("dmY");
		$chemin = _DIR_PLUGIN_GENESPIP."gedcom/";
		$fic=$chemin.$date_upload."-".$_FILES['gedcomfic']['name'];
		if (is_uploaded_file($_FILES['gedcomfic']['tmp_name'])) {
		   move_uploaded_file ( $_FILES['gedcomfic']['tmp_name'],$fic);
		   }
		echo "<u>"._T('genespip:debut_gedcom')." ".$fic."</u><br />";
		genespip_gedcom($fic);
		echo "<a href='".$url_action_accueil."'>&raquo;&nbsp;"._T('genespip:fermer')."</a>";
	}else{
		$ret .=	_T('genespip:mettre_jour_base_fichier_gedcom')."<br /><br />";
		$ret .= "<FORM ACTION='".$url_action_accueil."' method='POST' ENCTYPE='multipart/form-data'>";
		$ret .= "<input type='hidden' name='action' value='gedcom'>";
		$ret .= "<input type='hidden' name='max_file_size' value='1048576'>";
		$ret .= "Fichier Gedcom : <input type='file' name='gedcomfic' size='15'><br />";
		$ret .= "<INPUT TYPE='submit' name='telecharger' value='"._T('genespip:charger')."' class='fondo'>";
		$ret .= "</form>";
		echo $ret;
	}
	echo fin_cadre_relief();

	echo debut_cadre_relief('',true);
	echo gros_titre(_T('genespip:export_gedcom'), '', false);
	if ($_POST['action']=='gedcom_export'){
		genespip_gedcom_export();
	}
	echo "<br />";
	$ret1 .= "<FORM ACTION='".$url_action_accueil."' method='POST' ENCTYPE='multipart/form-data'>";
	$ret1 .= "<input type='hidden' name='action' value='gedcom_export'>";
	$ret1 .= "<INPUT TYPE='submit' name='exporter' value='"._T('genespip:exporter')."' class='fondo'>";
	$ret1 .= "</form>";
	echo $ret1;
	echo fin_cadre_relief(true);
	echo fin_page(true);
}
?>
