<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');

function exec_echoppe_edit_categorie(){
	
	
	$lang_categorie = _request('lang');
	$id_parent = _request('id_parent');
	$id_categorie = _request('id_categorie');
	
	if (_request('new') == "oui"){
		$titre = filtrer_entites(_T('echoppe:nouvelle_cetegorie'));
		$descriptif = "";
		$texte = "";
		$logo = "";
		$maj = '';
		$statut = 'redac';
	}else{
		$sql_descriptif_categorie = "SELECT * FROM spip_echoppe_categories_descriptions WHERE id_categorie = '".$id_categorie."'";
		if (!empty($lang_categorie)) $sql_descriptif_categorie .= " AND lang='".$lang_categorie."'";
		$res_descriptif_categorie = spip_query($sql_descriptif_categorie);
		$descriptif_categorie = spip_fetch_array($res_descriptif_categorie);
		
		$id_categorie = _request('id_categorie');
		$titre = $descriptif_categorie['titre'];
		$descriptif = $descriptif_categorie['descriptif'];
		$texte = $descriptif_categorie['texte'];
		$logo = $descriptif_categorie['logo'];
		$maj = $descriptif_categorie['maj'];
		$statut = $descriptif_categorie['statut'];
		
		$date_dernière_modification = affdate($maj);
		if (empty($date_dernière_modification)){
			$date_dernière_modification = _T('echoppe:pas_encore_cree');
		}else{
			if($date_dernière_modification == 0){
				$date_dernière_modification = _T('echoppe:pas_encore_modifie');
			}
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
	echo '<div style="font-size: 10;text-align: center;">'._T('echoppe:derniere_modification').' : <br /><b>'.$date_dernière_modification.'</b></div><br />';
	echo '<div style="font-size: 10;text-align: center;">'._T('echoppe:langue_editee').' : <br /><b>'.traduire_nom_langue($lang_categorie).'</b></div>';
	echo fin_boite_info();
	
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe",""), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
	$raccourcis .= icone_horizontale(_T('echoppe:retour'), generer_url_ecrire("echoppe_categorie","id_categorie=".$id_categorie."&lang=".$lang_categorie), _DIR_PLUGIN_ECHOPPE."images/retour.png","", false);
	
	echo bloc_des_raccourcis($raccourcis);
	
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:edition_de_cetegorie'));
	echo gros_titre(_T("echoppe:edition_de_cetegorie"));
	
	echo debut_cadre_formulaire();
	echo '<form action="'.generer_url_action("echoppe_sauver_categorie","").'" method="post" >';
	
	echo '<input type="hidden" name="lang" value="'.$lang_categorie.'" />';
	echo '<input type="hidden" name="id_parent" value="'.$id_parent.'" />';
	echo '<input type="hidden" name="redirect" value="'.generer_url_ecrire('echoppe').'" />';
	echo '<input type="hidden" name="new" value="'._request('new').'" />';
	echo '<input type="hidden" name="id_categorie_descriptif" value="'.$id_categorie.'" />';
	
	echo '<b>'._T('echoppe:titre_categorie').'</b><br />';
	echo '<input type=text class="forml" name="titre" value="'.$titre.'"/><br />';
	
	echo '<b>'._T('echoppe:descriptif').'</b><br />';
	echo '<textarea name="descriptif" class="forml" >'.$descriptif.'</textarea><br />';
	
	echo '<b>'._T('echoppe:texte').'</b><br />';
	echo barre_textarea ($texte, '20', $cols, $lang_categorie='');
	echo '<input type="submit" class="fondo" />';
	echo '</form>';
	echo fin_cadre_formulaire();
	
	
	echo fin_gauche();
	echo fin_page();
	
}

?>
