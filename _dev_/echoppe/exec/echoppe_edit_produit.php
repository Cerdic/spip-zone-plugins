<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');

function exec_echoppe_edit_produit(){
	
	$id_produit = _request('id_produit');
	$id_rubrique = _request('id_rubrique');
	$lang = _request('lang');
	$new = _request('new');
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_produits'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:edition_de_produit'));
	echo gros_titre(_T("echoppe:edition_de_produit"));
	
	if ($new=="oui"){
		$id_produit = "";
		
	}else{
		$sql_le_produit
		(spip_num_rows($res_descriptif_categorie) > 0)?$new = $new:$new = 'description';
	}
	echo debut_cadre_formulaire();
	echo '<form action="'.generer_url_action("echoppe_sauver_produit","id_produit=".$id_produit."&id_categorie=".$id_catecogie."&lang=".$lang."&new=".$new,'&').'" method="post" >';
	
	
	echo '<b>'._T('echoppe:titre_produit').'</b><br />';
	echo '<input type=text class="forml" name="titre" value="'.$titre.'"/><br />';
	echo '<b>'._T('echoppe:date_de_mise_en_vente').'</b>';
	echo '<input type="texte" class="forml" name="date_de_mise_en_vente" />';
	echo '<b>'._T('echoppe:date_de_retrait_de_vente').'</b>';
	echo '<input type="texte" class="forml" name="date_de_retrait_de_vente" />';
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
