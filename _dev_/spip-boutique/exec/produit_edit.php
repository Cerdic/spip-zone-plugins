<?php
include_spip("inc/boutique");
function exec_produit_edit(){
		global $connect_statut, $connect_id_auteur;
	echo debut_page(_T('boutique:editer_un_produit'));
		echo debut_gauche();
			echo debut_boite_info();
				echo _T('boutique:creer_un_produit');
			echo fin_boite_info();
				if (_request('id_produit') != "new"){
					echo bloc_des_raccourcis(icone_horizontale(_T('boutique:retour'), generer_url_ecrire("categories","id_produit="._request('id_produit').""), "", _DIR_PLUGIN_BOUTIQUE."img_pack/back_32.png", false));
				}else{
					echo bloc_des_raccourcis(icone_horizontale(_T('boutique:retour'), generer_url_ecrire("categories","id_categorie="._request('id_parent').""), "", _DIR_PLUGIN_BOUTIQUE."img_pack/back_32.png", false));
				}
		echo debut_droite();
			
		$res_produit = spip_query("SELECT * FROM spip_boutique_produits WHERE id_produit = '"._request('id_produit')."';");
		$produit = spip_fetch_array($res_categorie);
		if (spip_num_rows($res_produit) == 0 ){
			$id_parent = _request('id_parent');
		}else{
			$id_parent = $produit['id_parent'];
		}
		
		
		echo debut_cadre_formulaire();
		echo '<form action="'.lire_meta('adresse_site').'/spip.php" method="get" />';
		echo '<input type="hidden" name="action" value="sauver_produit" />';
		echo '<input type="hidden" name="id_produit" value="'._request('id_produit').'" />';
		echo '<input type="hidden" name="redirect" value="'.lire_meta('adresse_site').'/ecrire/?exec=categories" />';
		echo '<b>'._T('boutique:titre_de_la_categorie').'</b>';
		echo '<input style="width: 480px;" class="formo" name="titre" value="'.$produit['titre'].'" size="40" type="text"><br />';
		
		echo '<table>';
		echo '<tr><td>'._T('boutique:tva').'</td><td><input type="text" value="'.$produit['tva'].'" name="tva" /></td></tr>';
		echo '</table>';
		
		afficher_liste_parents($id_parent);
		
		echo '<br /><b>'._T('boutique:descriptif_de_la_categorie').'</b>';
		echo '<textarea name="descriptif" rows="5" class="forml" cols="">'.$produit['descriptif'].'</textarea><br /><br />';
		echo '<br /><b>'._T('boutique:texte_de_la_categorie').'</b>';
		echo barre_textarea ( $produit['texte'], '20', $cols, $lang='' );
		echo '<br /><div align="right"><input type="submit" class="fondo" value="'._T('boutique:enregistrer').'" /></div><br />';
		echo '</form>';
		echo fin_cadre_formulaire();
			
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}

?>
