<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/filtres');

function exec_echoppe_categorie(){
	
	if ($GLOBALS['connect_statut'] != "0minirezo"){
		die(echoppe_echec_autorisation().fin_page());
	}
	
	$lang_categorie = _request('lang');
	$id_categorie = _request('id_categorie');
	$new = _request('new');
	
	$sql_test_categorie_existe = "SELECT * FROM spip_echoppe_categories WHERE id_categorie = '".$id_categorie."';";
	$res_test_categorie_existe = spip_query($sql_test_categorie_existe);
	if (spip_num_rows($res_test_categorie_existe) == 0 && $new != 'oui'){
		die(inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe")._T('echoppe:pas_de_produit_ici').fin_page());
	}
	
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
	//if (spip_num_rows($res_select_categorie) > 0){
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
		
		if (count($les_langues) > 1){
			/*$les_langues = explode(',',$GLOBALS['meta']['langues_multilingue']);*/
			foreach ($les_langues as $key => $value){
				if ($value == $lang_categorie) {
					echo '<b>=> <a href="'.generer_url_ecrire('echoppe_categorie','id_categorie='.$id_categorie.'&lang='.$value).'">'.traduire_nom_langue($value).'</a></b><br />';
				}else{
					echo '<a href="'.generer_url_ecrire('echoppe_categorie','id_categorie='.$id_categorie.'&lang='.$value).'">'.traduire_nom_langue($value).'</a><br />';
				}
				
			}
		}
		echo fin_boite_info();
		
		
		$raccourcis .= icone_horizontale(_T('echoppe:creer_nouvelle_categorie'), generer_url_ecrire("echoppe_edit_categorie","new=oui&id_parent="._request('id_categorie')), _DIR_PLUGIN_ECHOPPE."images/categorie-24.png","creer.gif", false);
		$raccourcis .= icone_horizontale(_T('echoppe:nouveau_produit'), generer_url_ecrire("echoppe_edit_produit","new=oui&id_categorie="._request('id_categorie')), "","creer.gif", false);
		$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe",""), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
		echo bloc_des_raccourcis($raccourcis);
		
		
		echo creer_colonne_droite();
		echo debut_droite(_T('echoppe:echoppe'));
		echo '<div class="cadre-r"><div class="cadre-padding">';
	
	
	
		echo gros_titre($categorie['titre']);
		echo icone_horizontale(_T('echoppe:editer_categorie'), generer_url_ecrire("echoppe_edit_categorie","id_categorie=".$id_categorie."&lang=".$lang_categorie), _DIR_PLUGIN_ECHOPPE."images/categorie-24.png","edit.gif", false);
		echo '<br /><div class="verdana1 spip_small" align="left" style="border: 1px dashed rgb(170, 170, 170); padding: 5px;">'.$categorie['descriptif'].'</div>';
		echo '</div></div>';
		
			$sql_categories = "SELECT cat.id_categorie, cat_desc.titre, cat_desc.descriptif FROM spip_echoppe_categories cat, spip_echoppe_categories_descriptions cat_desc WHERE id_parent = '".$id_categorie."' AND cat.id_categorie = cat_desc.id_categorie AND cat_desc.lang = '' ORDER BY cat.id_categorie;";
			//echo $sql_categories;
			$res_categories = spip_query($sql_categories);
			
			while($categorie = spip_fetch_array($res_categories)){
				//var_dump($categorie);
				echo '
					<div class="cadre-sous_rub" style="width: 45%;float: left; margin: 10px 0px 0px 10px;">
						<div class="cadre-padding">
							<b><a href="'.generer_url_ecrire('echoppe_categorie', 'id_categorie='.$categorie['id_categorie']).'">'.$categorie['titre'].'</a></b><br />
							<div class="verdana1">'.couper($categorie['descriptif'],'150').'</div>
						</div>
					</div>';
			}
			
			$sql_les_produits = "SELECT ecp.*, ecd.* FROM spip_echoppe_categories_produits ecp, spip_echoppe_produits_descriptions edp WHERE ecp.id_categorie = '".$id_categorie."' AND epd.id_produit = ecp.id_produit";
			echo $sql_les_produits;
			$res_les_produits = spip_query($sql_les_produits);
			while($les_produits = spip_fetch_array($res_les_produits)){
				echo '<a href="" ><b>'.$les_produits.'</b></a><br />';
			}
			
	/*}else{
		echo _T('echoppe:pas_de_categorie_ici');
	}*/
	
	echo fin_gauche();
	echo fin_page();	
}

?>
