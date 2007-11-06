<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');

function exec_echoppe_edit_categorie(){
	
	
	$lang = _request('lang');
	$id_parent = _request('id_parent');
	if (_request == "new"){
		$id_categorie = _request('id_categorie');
		$titre = filtrer_entites(_T('echoppe:nouvelle_cetegorie'));
		$descriptif = "";
		$texte = "";
		$logo = "";
		$maj = '';
		$statut = 'redac';
	}else{
		$sql_descriptif_categorie = "SELECT * FROM spip_echoppe_categorie_descriptif WHERE id_categorie = '".$id_categorie."'";
		if ($lang != Null) $sql_descriptif_categorie .= " AND lang='".$lang."'";
		$res_descriptif_categorie = spip_query($sql_descriptif_categorie);
		$descriptif_categorie = spip_fetch_array($res_descriptif_categorie);
		
		$id_categorie = _request('id_categorie');
		$titre = $descriptif_categorie['titre'];
		$descriptif = $descriptif_categorie['descriptif'];
		$texte = $descriptif_categorie['texte'];
		$logo = $descriptif_categorie['logo'];
		$maj = $descriptif_categorie['maj'];
		$statut = $descriptif_categorie['statut'];
	}


	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_categories'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_categories'), "redacteurs", "echoppe");
	}

	echo debut_gauche();
	
	echo debut_boite_info();
	echo '<div style="font-weight:bold;text-align: center; text-transform : uppercase;">'._T('echoppe:categorie_numero').'</div>';
	echo '<div style="font-size: 20;font-weight:bold;">'.$id_categorie.'</div>';
	echo '<div style="font-size: 10;">'.affdate($maj).'</div>';
	echo fin_boite_info();
	
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe",""), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
	echo bloc_des_raccourcis($raccourcis);
	
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:nouvelle_cetegorie'));
	echo gros_titre(_T("echoppe:nouvelle_cetegorie"));
	
	echo debut_cadre_formulaire();
	echo '<form action="'.generer_url_action("echoppe_sauver_categorie","new=oui").'" method="post" >';
	
	echo '<input type="hidden" name="lang" value="'.$lang.'" />';
	echo '<input type="hidden" name="id_parent" value="'.$id_parent.'" />';
	echo '<input type="hidden" name="redirect" value="'.generer_url_ecrire('echoppe').'" />';
	echo '<input type="hidden" name="new" value="'._request('new').'" />';
	echo '<input type="hidden" name="id_categorie_descriptif" value="'.$id_categorie.'" />';
	
	echo '<b>'._T('echoppe:titre_categorie').'</b><br />';
	echo '<input type=text class="forml" name="titre"/><br />';
	
	echo '<b>'._T('echoppe:descriptif').'</b><br />';
	echo '<textarea name="descriptif" class="forml" >'.$descriptif.'</textarea><br />';
	
	echo '<b>'._T('echoppe:texte').'</b><br />';
	echo barre_textarea ($texte, '20', $cols, $lang='');
	echo '<input type="submit" class="fondo" />';
	echo '</form>';
	echo fin_cadre_formulaire();
	
	
	echo fin_gauche();
	echo fin_page();
	
}

?>
