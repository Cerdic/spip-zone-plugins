<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/filtres');
include_spip('public/assembler');

function exec_echoppe_categorie(){
	
	if ($GLOBALS['connect_statut'] != "0minirezo"){
		echo(echoppe_echec_autorisation().fin_page());
		return;
	}
	
	$contexte['lang_categorie'] = _request('lang_categorie');
	$contexte['id_categorie'] = _request('id_categorie');
	$contexte['new'] = _request('new');
	
	$sql_test_categorie_existe = "SELECT * FROM spip_echoppe_categories WHERE id_categorie = '".$contexte['id_categorie']."';";
	$res_test_categorie_existe = spip_query($sql_test_categorie_existe);
	if (sql_count ($res_test_categorie_existe) != 1 && $new != 'oui'){
		die(inc_commencer_page_dist(_T('echoppe:les_categories'), "redacteurs", "echoppe")._T('echoppe:pas_de_categorie_ici').fin_page());
	}
	
	$sql_select_categorie = "SELECT cat.*, cat_desc.* FROM spip_echoppe_categories cat, spip_echoppe_categories_descriptions cat_desc WHERE cat.id_categorie = '".$contexte['id_categorie']."' AND cat.id_categorie = cat_desc.id_categorie AND cat_desc.lang='".$contexte['lang_categorie']."';";
	$res_select_categorie = spip_query($sql_select_categorie);
	$categorie = spip_fetch_array($res_select_categorie);
	
	(is_array($categorie))?$contexte = array_merge($contexte,$categorie):$contexte = $contexte;
	
	$date_derniere_modification = affdate($contexte['maj']);
	if (empty($date_derniere_modification)){
		$date_derniere_modification = _T('echoppe:pas_encore_cree');
	}else{
		if($date_derniere_modification == 0){
			$date_derniere_modification = _T('echoppe:pas_encore_modifie');
		}
	}
	
	
	echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	
	
	echo debut_gauche('',true);
	echo recuperer_fond('fonds/echoppe_chemin_categorie',$contexte);
	echo debut_boite_info(true);
	echo recuperer_fond('fonds/echoppe_info_categorie',$contexte);

	$les_langues = explode(',',$GLOBALS['meta']['langues_multilingue']);

	//if (count($les_langues) > 1){
		echo '<form action="index.php" method="get">
		<input type="hidden" name="exec" value="echoppe_edit_categorie" />
		<input type="hidden" name="id_categorie" value="'.$contexte['id_categorie'].'" />
		<select name="lang_categorie">';
		echo '<option value="">'._T('echoppe:par_defaut').'</option>';
		foreach ($les_langues as $value) {
			echo '<option value="'.$value.'">'.traduire_nom_langue($value).'</option>';
		}
		echo '</form>
		<input type="submit" value="'._T('echoppe:editer').'" />
		</select>';
	//}
	echo fin_boite_info(true);
	
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	
	echo debut_droite(true,_T('echoppe:echoppe'));
	
	echo recuperer_fond('fonds/echoppe_categorie', $contexte);
	
	echo fin_gauche(true);
	echo fin_page(true);	
}

?>
