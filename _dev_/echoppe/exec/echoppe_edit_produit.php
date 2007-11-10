<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');

function exec_echoppe_edit_produit(){
	
	$id_produit = _request('id_produit');
	$id_rubrique = _request('id_rubrique');
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:les_produits'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:les_produits'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();
	
	echo creer_colonne_droite();
	echo debut_droite(_T('echoppe:edition_de_produit'));
	echo gros_titre(_T("echoppe:edition_de_produit"));
	
	echo debut_cadre_formulaire();
	echo '<form action="'.generer_url_action("echoppe_sauver_produit","").'" method="post" >';
	
	
	echo '<b>'._T('echoppe:titre_produit').'</b><br />';
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
