<?php
//
//	categorie_edit.php
//
include_spip("inc/boutique");

function exec_categorie_edit(){
	global $connect_statut, $connect_id_auteur;
	echo debut_page(_T('boutique:editer_une_categorie'));
		echo debut_gauche();
			echo debut_boite_info();
				echo _T('boutique:creer_categorie');
			echo fin_boite_info();
				if (_request('id_categorie') != "new"){
					echo bloc_des_raccourcis(icone_horizontale(_T('boutique:retour'), generer_url_ecrire("categories","id_categorie="._request('id_categorie').""), "", _DIR_PLUGIN_BOUTIQUE."img_pack/back_32.png", false));
				}else{
					echo bloc_des_raccourcis(icone_horizontale(_T('boutique:retour'), generer_url_ecrire("categories","id_categorie="._request('id_parent').""), "", _DIR_PLUGIN_BOUTIQUE."img_pack/back_32.png", false));
				}
		echo debut_droite();
			
		$res_categorie = spip_query("SELECT * FROM spip_boutique_categories WHERE id_categorie = '"._request('id_categorie')."';");
		$categorie = spip_fetch_array($res_categorie);
		if (spip_num_rows($res_categorie) == 0 ){
			$id_parent = _request('id_parent');
		}else{
			$id_parent = $categorie['id_parent'];
		}
		
		
		echo debut_cadre_formulaire();
		echo '<form action="'.lire_meta('adresse_site').'/spip.php" method="get" />';
		echo '<input type="hidden" name="action" value="sauver_categorie" />';
		echo '<input type="hidden" name="id_categorie" value="'._request('id_categorie').'" />';
		echo '<input type="hidden" name="redirect" value="'.lire_meta('adresse_site').'/ecrire/?exec=categories" />';
		echo '<b>'._T('boutique:titre_de_la_categorie').'</b>';
		echo '<input style="width: 480px;" class="formo" name="titre" value="'.$categorie['titre'].'" size="40" type="text"><br />';
		
		
		afficher_liste_parents($id_parent);
		
		echo '<br /><b>'._T('boutique:descriptif_de_la_categorie').'</b>';
		echo '<textarea name="descriptif" rows="5" class="forml" cols="">'.$categorie['descriptif'].'</textarea><br /><br />';
		echo '<br /><b>'._T('boutique:texte_de_la_categorie').'</b>';
		echo barre_textarea ( $categorie['texte'], '20', $cols, $lang='' );
		echo '<br /><div align="right"><input type="submit" class="fondo" value="'._T('boutique:enregistrer').'" /></div><br />';
		echo '</form>';
		echo fin_cadre_formulaire();
			
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}

?>
