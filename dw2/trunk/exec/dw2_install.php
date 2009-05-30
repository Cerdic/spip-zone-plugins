<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| page Installation ou Mise a Jour
+--------------------------------------------+
*/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_install() {

	// elements spip
	global 	$connect_statut,
			$connect_id_auteur,
			$connect_toutes_rubriques,
			$couleur_claire, $couleur_foncee;
	
	
	// fonctions requises ...
	include_spip("inc/dw2_inc_admin");
	include_spip("inc/dw2_inc_func");
	include_spip("inc/dw2_inc_pres");
	include_spip("inc/dw2_inc_install");
	include_spip("inc/dw2_inc_config");


//
// Cas install -> ecrire tables
//	
	if($GLOBALS['flag_dw2_inst'] == 'ins') {
		ecriture_tables_dw2();
		$info_tbl = _T('dw:txt_install_16');
		//
		dw2_init_param(_DW2_VERS_PLUGIN);
	}


//
// Cas maj tables
//
	if($GLOBALS['flag_dw2_inst'] == 'maj') {
		
		# recuperer ancienne version installee, qq_elle soit !!
		# et init globale dw2_param
		if(!$GLOBALS['dw2_param']['version_installee']) {
			include_spip("inc/dw2_inc_install2");
			convertir_anc_param();
		}
		
		// 1 - faire les maj des anc. tables
		maj_tables_dw2($GLOBALS['dw2_param']['version_installee']);
		
		// 2 - repasser en crea de tables pour d_eventuelles nouv.
		ecriture_tables_dw2();
		$info_tbl = _T('dw:txt_install_10');
		
		// 3 - completer tableau param si nouveaux #h.25/12
		dw2_init_param($GLOBALS['dw2_param']['version_installee']);
	}




//
// affichage
//

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('dw:titre_page_admin'), "suivi", "dw2_admin");
	
	echo "<a name='haut_page'></a><br />";
	
	echo gros_titre(_T('dw:titre_page_admin'),'','',true);
	echo debut_gauche('',true);
	
	
	echo creer_colonne_droite('',true);
		// vers aide koakidi.com 
		bloc_ico_aide_ligne();
	
		// signature
		echo "<br />";
		echo debut_boite_info(true);
			echo _T('dw:signature', array('version' => $GLOBALS['dw2_param']['version_installee']));
		echo fin_boite_info(true);
		echo "<br />";
	
	
	echo debut_droite('',true);
	
	
	echo debut_cadre_trait_couleur(_DIR_IMG_DW2."configure.gif", true, "", _T('dw:txt_install_09',array('vers_loc' => _DW2_VERS_PLUGIN)));
		
		// petite phrase install/maj tables
		echo "<br /><span class='verdana3'><b>".$info_tbl."</b></span><br />";

	echo fin_cadre_trait_couleur(true);
	

	// le formulaire de config
	formulaire_configuration();
	
	
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";
	
	echo fin_gauche().fin_page();

}
?>
