<?php
//
//	categories_page.php
//

include_spip("inc/presentation");

function exec_categories_page(){
	global $connect_statut, $connect_id_auteur;
	echo debut_page(_T('boutique:gerer_les_categories'));
		echo debut_gauche();
			echo pipeline('affiche_gauche',array('args'=>array('exec'=>'categories_page'),'data'=>''));
			echo debut_boite_info();
				echo _T('boutique:gerer_les_categories');
			echo fin_boite_info();
				$res_categorie_exist = spip_query("SELECT id_categorie FROM spip_boutique_categories LIMIT 1");
				if (spip_num_rows($res_categorie_exist) > 0) {
					echo bloc_des_raccourcis(icone_horizontale(_T('boutique:icone_creer_categorie'), generer_url_ecrire("categorie_edit","id_categorie=new"), _DIR_PLUGIN_BOUTIQUE."/img_pack/caissons_categorie.png", "creer.gif", false));
				} else {
					if ($connect_statut == '0minirezo') {
						echo	icone_horizontale (_T('boutique:creer_categorie'), generer_url_ecrire("categorie_edit","id_categorie=new"), _DIR_PLUGIN_BOUTIQUE."/img_pack/caissons_categorie.png", "creer.gif",false);
					}
				}
		echo debut_droite();
		
		if (spip_num_rows($res_categorie_exist) > 0 ){
			$res_liste_categories_racine = spip_query("SELECT id_categorie, titre, descriptif FROM spip_boutique_categories WHERE id_parent = '0';");
			while ($categorie = spip_fetch_array($res_liste_categories_racine)){
				//echo debut_cadre_trait_couleur();
				echo debut_cadre_sous_rub ( _DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png", false, "", "" );
					echo  '<span dir="ltr"><b><a href="'.lire_meta('adresse_site').'/ecrire/?exec=categories&amp;id_categorie='.$categorie['id_categorie'].'">'.$categorie['titre'].'</b></a></span><br /><div class="verdana1">'.propre($categorie['descriptif']).'</div>';
				echo fin_cadre_sous_rub (false);
				//echo fin_cadre_trait_couleur();
			}
			
		}else{
			echo debut_cadre_trait_couleur();
			if ($connect_statut == '0minirezo') {
				echo	icone_horizontale (_T('boutique:creer_categorie'), generer_url_ecrire("categorie_edit","id_categorie=new"), _DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png", "creer.gif",false);
			}
			echo fin_cadre_trait_couleur();
		}
		
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}
?>
