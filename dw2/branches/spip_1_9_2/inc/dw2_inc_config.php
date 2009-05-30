<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| formulaire de configuration
+--------------------------------------------+
*/


//
// Constructeur de <tr> du formulaire de config
//
function aff_ligne_config($param, $titre_ligne, $type, $options, $mode_aff_var, $txt_instal, $val_param) {


	// aff. valeur actuelle
	if($mode_aff_var=='trad' && $type=='checkbox') {
		if($val_param) {
			$tab_chaine=explode(',',$val_param);
			foreach($tab_chaine as $chaine) {
				$aff_val_actu.="&nbsp;"._T('dw:cfg_'.$param.'_val_'.$chaine).",";
			}
		}
	}
	elseif($mode_aff_var=='trad')
		{ $aff_val_actu.="&nbsp;". _T('dw:cfg_'.$param.'_val_'.$val_param); }
	else
		{ $aff_val_actu.="&nbsp;".$val_param; }
			
	debut_cadre_trait_couleur("", false, "", _T('dw:txt_install_'.$titre_ligne)." ".$aff_val_actu);
	
	echo "\n<table width='100%' cellspacing='0' cellpadding='3' border='0' />\n";
	echo "<tr><td width='70%'>";
	echo "<span class='verdana2'>"._T('dw:txt_install_'.$txt_instal)."</span>\n";
	echo "</td><td width='5%'> </td><td width='25%' valign='middle'>";
	
	// aff. selecteurs ...
	//
	if($type=='text') {
		$maxl = (!$options) ? '3' : '';
		$tsize = ($options) ? $options : '4';
		echo "<input type='text' name='".$param."' value='".$val_param."' size='".$tsize."' maxlength='".$maxl."' class='fondl' />\n";
	}
	elseif($type=='select') {
		echo "<select name='".$param."' class='fondl'>";
		foreach($options as $val) {
			$aff_selected = ($val_param==$val) ? ' selected=\"selected\"' : '' ;
			echo "<option value='".$val."'".$aff_selected.">";
			if($mode_aff_var=='trad')
				{ echo _T('dw:cfg_'.$param.'_val_'.$val); }
			else
				{ echo $val; }
			echo "</option>";
		}
		echo "</select>\n";
	}
	#h.25/12 au cas ou !
	elseif($type=='radio') {	
		foreach($options as $val) {
			$aff_checked = ($val_param==$val) ? 'checked=\"checked\"' : '' ;
			echo "<input type='radio' name='".$param."' value='".$val."' ".$aff_checked." />&nbsp;";
			if($mode_aff_var=='trad')
				{ echo _T('dw:cfg_'.$param.'_val_'.$val); }
			else
				{ echo $val; }
			echo "<br />\n";
		}
	}
	elseif($type=='checkbox') {
		$tb_val=explode(',',$val_param);
		if(!is_array($tb_val)) { 
			$tb_val=array();
		}
		// on passe toujours le tableau(menage fait dans 'action')
		echo "<input type='hidden' name='".$param."[]' value='0' />\n";
		
		foreach($options as $val) {
			$aff_checked = (in_array($val,$tb_val))? 'checked=\"checked\"' : '';
			echo "<input type='checkbox' name='".$param."[]' value='".$val."' ".$aff_checked." />&nbsp;";
			if($mode_aff_var=='trad')
				{ echo _T('dw:cfg_'.$param.'_val_'.$val); }
			else
				{ echo $val; }
			echo "<br />\n";
		}
	}
	echo "\n</td></tr></table>\n";
	fin_cadre_trait_couleur();

}


//
// generer le formulaire de config	
//

function formulaire_configuration() {

	global $connect_id_auteur, $couleur_foncee;


	// titre page
	debut_cadre_couleur(_DIR_IMG_DW2."configure.gif", false, "",'' );
	debut_band_titre($couleur_foncee,'verdana3');
	echo _T('dw:txt_install_01', array('vers_loc' => _DW2_VERS_PLUGIN));
	fin_bloc();
	
	//
	// message
	if($GLOBALS['dw2_param']['mode_restreint']=='oui' && $GLOBALS['dw2_param']['forcer_url_dw2']=='non') {
		debut_boite_filet('c');
			echo _T('dw:message_config_01');
		fin_bloc();
	}
	
	//
	// redirection action - cas 'install/maj' ou 'modif config'
	if($GLOBALS['flag_dw2_inst']) {
		$url_redirect = generer_url_ecrire("dw2_admin");
	} else {
		$url_redirect = $url_redirect = generer_url_ecrire("dw2_config");
	}
	
	echo "<form action='".generer_url_action("dw2_ecrireconfig", "arg="._DW2_VERS_PLUGIN)."' method='post' />\n";
	echo "<input type='hidden' name='redirect' value='".$url_redirect."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2_ecrireconfig "._DW2_VERS_PLUGIN)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	
	// signaler a 'action' -> install/maj
	if($GLOBALS['flag_dw2_inst']) {
		echo "<input type='hidden' name='change_version' value='oui' />\n";
	}
	
	echo "<p style='padding:3px;'><b> ".
			($GLOBALS['flag_dw2_inst']? _T('dw:txt_install_15'):_T('dw:actuellement')).
			" </b></p>\n";
	//
	// prepare les lignes des params config
	// definir specificites d_affichage
	//
	foreach ($GLOBALS['dw2_param'] as $item => $v) {
		
		$titre_ligne = '';
		$type = '';
		$options = '';
		$mode_aff_var = '';
		$txt_instal = '';
				
		switch ($item) {
			case 'anti_triche':
				$titre_ligne = "02";
				$type = "select";
				$options = array('oui', 'non');
				$mode_aff_var = 'trad';
				$txt_instal = "11";
			break;
			case 'nbr_lignes_tableau':
				$titre_ligne = "03";
				$type = "text";
				$options = '';
				$mode_aff_var = 'brut';
				$txt_instal = "14";
			break;
			case 'type_categorie':
				$titre_ligne = "07";
				$type = "select";
				$options = array('secteur', 'rubrique');
				$mode_aff_var = 'trad';
				$txt_instal = "08";
			break;
			case 'extens_logo_serveur':
				$titre_ligne = "04";
				$type = "select";
				$options = array('gif', 'png', 'jpg');
				$mode_aff_var = 'brut';
				$txt_instal = "05";
			break;
			case 'mode_enregistre_doc':
				$titre_ligne = "17";
				$type = "select";
				$options = array('manuel', 'auto');
				$mode_aff_var = 'trad'; //h.25/12 passer en 'trad' et faire chaine-text
				$txt_instal = "19";
			break;
			case 'jours_affiche_nouv':
				$titre_ligne = "18";
				$type = "text";
				$options = '';
				$mode_aff_var = 'brut';
				$txt_instal = "21";
			break;
			case 'mode_affiche_images':
				$titre_ligne = "23";
				$type = "select";
				$options = array('1', '2');
				$mode_aff_var = 'trad';
				$txt_instal = '22';
			break;
			/*
			# h.11/02/07 .. CRON !!
			case 'avis_maj':
				$titre_ligne = "24";
				$type = "select";
				$options = array('oui', 'non');
				$mode_aff_var = 'trad';
				$txt_instal = "25";
			break;*/
			case 'squelette_cata_public':
				$titre_ligne = "26";
				$type = "text";
				$options = '15';
				$mode_aff_var = 'brut';
				$txt_instal = "27";
			break;
			#h.25/12 restreint ..
			case 'mode_restreint':
				$titre_ligne = "28";
				$type = "select";
				$options = array('oui', 'non');
				$mode_aff_var = 'trad';
				$txt_instal = "29";
			break;
			#h.02/02 
			case 'criteres_auto_doc':
				$titre_ligne = "30";
				$type = "checkbox";
				$options = array('1','2','3'); //jpg,png,gif
				$mode_aff_var = 'trad';
				$txt_instal = "31";
			break;
			#h03/03
			case 'forcer_url_dw2':
				$titre_ligne = "32";
				$type = "radio";
				$options = array('oui','non');
				$mode_aff_var = 'trad';
				$txt_instal = "33";
			break;
			
		}
		
		// affiche le bloc du param
		if($titre_ligne) {
			aff_ligne_config($item, $titre_ligne, $type, $options, $mode_aff_var, $txt_instal, $v);		
		}
		else {
			// autres parametres non modifiables
			if($item=='avis_maj') {
				echo "<input type='hidden' name='".$item."' value='".$v."' />\n";
			}
			elseif($item=='message_maj') {
				if(empty($v)) { $v=' '; }
				echo "<input type='hidden' name='".$item."' value='".$v."' />\n";
			}
		}
	}
	

	echo "<br /><div align='right'><input type='submit' value='"._T('dw:valider')."' class='fondo'></div>\n";
	echo "</form>";
	
	// date de la derniere verif de MaJ
	$q=spip_query("SELECT DATE_FORMAT(maj, '%d/%m/%Y %H:%i') as datemaj FROM spip_dw2_config WHERE nom='avis_maj'");
	$r=spip_fetch_array($q);
	echo "<br />";
	debut_boite_filet('b', 'center');
	echo _T('dw:date_verif_avis_maj_plugin', array('datemaj' => $r['datemaj']));
	fin_bloc();
	
	fin_cadre_couleur();
}
?>
