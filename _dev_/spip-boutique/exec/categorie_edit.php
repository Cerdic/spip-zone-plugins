<?php
//
//	categorie_edit.php
//

function exec_categorie_edit(){
	global $connect_statut, $connect_id_auteur;
	echo debut_page(_T('boutique:editer_une_categorie'));
		echo debut_gauche();
			echo debut_boite_info();
				echo _T('boutique:creer_categories');
			echo fin_boite_info();
			echo debut_raccourcis();
				if (_request('id_categorie') != "new"){
					echo '<a href="?exec=produits_page"><b><img src="'._DIR_PLUGIN_BOUTIQUE.'img_pack/back.png" alt="retour" align="absmiddle"> Retour</b></a>';
				}
			echo fin_raccourcis();
		echo debut_droite();
			
			$res_categorie = spip_query("SELECT * FROM spip_boutique_categories WHERE id_categorie = '"._request('id_categorie')."';");
			$categorie = spip_fetch_array($res_categorie);
			
			echo debut_cadre_formulaire();
			echo '<b>'._T('boutique:titre_de_la_categorie').'</b>';
			echo '<input style="width: 480px;" class="formo" name="titre" value="'.$categorie['titre'].'" size="40" type="text"><br />';
			echo '<b>'._T('boutique:descriptif_de_la_categorie').'</b>';
			echo '<textarea name="descriptif" rows="5" class="forml" cols=""></textarea><br />';
			echo barre_textarea ( $contenu['descriptif'], '10', $cols, $lang='' );
			echo fin_cadre_formulaire();
			
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}
?>
