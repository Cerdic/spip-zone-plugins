<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_edit_categorie(){
	
	$contexte = array();
	$contexte['lang_categorie'] = _request('lang_categorie');
	$contexte['id_parent'] = _request('id_parent');
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['new'] = _request('new');
	
	if ($contexte['new'] != "oui"){
		$sql_descriptif_categorie = "SELECT * FROM spip_echoppe_categories_descriptions WHERE id_categorie = '".$contexte['id_categorie']."' AND lang='".$contexte['lang_categorie']."'";
		$res_descriptif_categorie = spip_query($sql_descriptif_categorie);
		$descriptif_categorie = spip_fetch_array($res_descriptif_categorie);
		
		(spip_num_rows($res_descriptif_categorie) > 0)?$new = $new:$new = 'description';
		(is_array($descriptif_categorie))?$contexte = array_merge($contexte, $descriptif_categorie):$contexte = $contexte;
		
	}
	
	$date_derniere_modification = affdate($contexte['maj']);
	if (empty($date_derniere_modification)){
		$date_derniere_modification = _T('echoppe:pas_encore_cree');
	}else{
		if($date_derniere_modification == 0){
			$date_derniere_modification = _T('echoppe:pas_encore_modifie');
		}
	}	
	$nom_lang = (traduire_nom_langue($lang_categorie))?traduire_nom_langue($lang_categorie):_T('echoppe:par_defaut');

	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_categories'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_categories'), "redacteurs", "echoppe");
	}

	echo debut_gauche();
	
	/*echo debut_boite_info();
	echo recuperer_fond('fonds/echoppe_info_edit_categorie',$contexte);
	echo fin_boite_info();*/
	
	($contexte['new'] == 'oui' && $contexte['id_parent'] != 0)?$raccourcis .= icone_horizontale(_T('echoppe:retour'), generer_url_ecrire("echoppe_categorie","id_categorie=".$contexte['id_parent']."&lang=".$contexte['lang_categorie']), _DIR_PLUGIN_ECHOPPE."images/retour.png","", false)."<hr />":$raccourcis=$raccourcis;	
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe",""), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
	echo bloc_des_raccourcis($raccourcis);
	
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:edition_de_cetegorie'));
	echo gros_titre(_T("echoppe:edition_de_cetegorie"));
	
	echo recuperer_fond('fonds/echoppe_edit_categorie',$contexte);
	
	echo fin_gauche();
	echo fin_page();
	
}

?>
