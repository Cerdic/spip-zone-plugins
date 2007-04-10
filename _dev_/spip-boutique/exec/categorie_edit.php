<?php
//
//	categorie_edit.php
//

function exec_categorie_edit(){
	global $connect_statut, $connect_id_auteur;
	echo debut_page(_T('boutique:editer_une_categorie'));
		echo debut_gauche();
			echo debut_boite_info();
				echo _T('boutique:creer_categorie');
			echo fin_boite_info();
			//echo debut_raccourcis();
				if (_request('new') != "new"){
					echo bloc_des_raccourcis(icone_horizontale(_T('boutique:retour'), generer_url_ecrire("produits_edit","new=oui"), "", _DIR_PLUGIN_BOUTIQUE."img_pack/back_32.png", false));
				}
			//echo fin_raccourcis();
		echo debut_droite();
			
			$res_categories = spip_query("SELECT * FROM spip_boutique_categories;");
			$res_categorie = spip_query("SELECT * FROM spip_boutique_categories WHERE id_categorie = '"._request('id_categorie')."';");
			$categorie = spip_fetch_array($res_categorie);
			
			echo debut_cadre_formulaire();
			echo '<b>'._T('boutique:titre_de_la_categorie').'</b>';
			echo '<input style="width: 480px;" class="formo" name="titre" value="'.$categorie['titre'].'" size="40" type="text"><br />';
			
			
			echo debut_cadre_couleur(_DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png",false,false,_T('boutique:dans_la_categorie'));
			if (mysql_num_rows($res_categories) > 0){
				echo '<select name="id_parent" class="formo">';
				echo '<option value="0" >'._T('boutique:racine_de_la_boutique').'</option>';
				while ($categories = spip_fetch_array('$res_categories')){
					echo '<option value="'.$categories.'" >'.$categories.'</option>';
				}
				echo '</select><br />';
			}else{
				echo '<input type="hidden" name="id_parent" value="0" />'._T('boutique:racine_de_la_boutique');
			}
			echo fin_cadre_couleur();
			
			echo '<br /><b>'._T('boutique:descriptif_de_la_categorie').'</b>';
			echo '<textarea name="descriptif" rows="5" class="forml" cols=""></textarea><br /><br />';
			echo '<br /><b>'._T('boutique:texte_de_la_categorie').'</b>';
			echo barre_textarea ( $contenu['descriptif'], '20', $cols, $lang='' );
			echo '<br /><div align="right"><input type="submit" class="fondo" value="'._T('boutique:enregistrer').'" /></div><br />';
			echo fin_cadre_formulaire();
			
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}
?>
