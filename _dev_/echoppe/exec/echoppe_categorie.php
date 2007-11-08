<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/date');

function exec_echoppe_categorie(){
	
	if ($GLOBALS['connect_statut'] != "0minirezo"){
		echo echoppe_echec_autorisation().fin_page();
	}
	
	$lang_categorie = _request('lang');

	
	$id_categorie = _request('id_categorie');
	$sql_select_categorie = "SELECT cat.*, cat_desc.* FROM spip_echoppe_categories cat, spip_echoppe_categories_descriptions cat_desc WHERE cat.id_categorie = '".$id_categorie."' AND cat.id_categorie = cat_desc.id_categorie AND cat_desc.lang='".$lang_categorie."';";
	$res_select_categorie = spip_query($sql_select_categorie);
	$categorie = spip_fetch_array($res_select_categorie);
	
	$date_derniere_modification = affdate($categorie['maj']);
	if (empty($date_derniere_modification)){
		$date_derniere_modification = _T('echoppe:pas_encore_cree');
	}else{
		if($date_derniere_modification == 0){
			$date_derniere_modification = _T('echoppe:pas_encore_modifie');
		}
	}
	
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_categories'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_categories'), "redacteurs", "echoppe");
	}
	
	
	
	echo debut_gauche();
	
	echo debut_boite_info();
	echo '<div style="font-weight:bold;text-align: center; text-transform : uppercase;">'._T('echoppe:categorie_numero').'</div>';
	echo '<div style="font-weight:bold;text-align: center;" class="spip_xx-large">'.$id_categorie.'</div>';
	echo '<div style="font-size: 10;text-align: center;">'._T('echoppe:derniere_modification').' : <br /><b>'.$date_derniere_modification.'</b></div>';
	echo '<br />';
	echo _T('echoppe:editer_version').'<br />';
	if (empty($lang_categorie)) {
		echo '<b>=> <a href="'.generer_url_ecrire('echoppe_categorie','id_categorie='.$id_categorie.'&lang=').'">'._T('echoppe:par_defaut').'</a></b><br />';
	}else{
		echo '<a href="'.generer_url_ecrire('echoppe_categorie','id_categorie='.$id_categorie.'&lang=').'">'._T('echoppe:par_defaut').'</a><br />';
	}
	$les_langues = explode(',',$GLOBALS['meta']['langues_multilingue']);
	foreach ($les_langues as $key => $value){
		if ($value == $lang_categorie) {
			echo '<b>=> <a href="'.generer_url_ecrire('echoppe_categorie','id_categorie='.$id_categorie.'&lang='.$value).'">'.traduire_nom_langue($value).'</a></b><br />';
		}else{
			echo '<a href="'.generer_url_ecrire('echoppe_categorie','id_categorie='.$id_categorie.'&lang='.$value).'">'.traduire_nom_langue($value).'</a><br />';
		}
		
	}
	echo fin_boite_info();
	
	
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe",""), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
	echo bloc_des_raccourcis($raccourcis);
	
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:echoppe'));
	echo '<div class="cadre-r"><div class="cadre-padding">';
	
	echo gros_titre($categorie['titre']);
	echo icone_horizontale(_T('echoppe:editer_categorie'), generer_url_ecrire("echoppe_edit_categorie","id_categorie=".$id_categorie."&lang=".$lang_categorie), _DIR_PLUGIN_ECHOPPE."images/categorie-24.png","edit.gif", false);
	echo '<br /><div class="verdana1 spip_small" align="left" style="border: 1px dashed rgb(170, 170, 170); padding: 5px;">'.$categorie['descriptif'].'</div>';
	echo '</div></div>';
	
	echo fin_gauche();
	echo fin_page();	
}

?>
