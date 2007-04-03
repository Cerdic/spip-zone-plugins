<?php
//
//	categories_page.php
//

function exec_categories_page(){
	global $connect_statut, $connect_id_auteur;
	echo debut_page(_T('boutique:gerer_les_categories'));
		echo debut_gauche();
			echo pipeline('affiche_gauche',array('args'=>array('exec'=>'categories_page'),'data'=>''));
			echo debut_boite_info();
				echo _T('boutique:gerer_les_categories');
			echo fin_boite_info();
			echo debut_raccourcis();
				$res_categorie_exist = spip_query("SELECT id_categorie FROM spip_boutique_categories LIMIT 1");
				if (spip_num_rows($res_categorie_exist) > 0) {
				echo bloc_des_raccourcis(icone_horizontale(_T('boutique:icone_creer_categorie'), generer_url_ecrire("produits_edit","new=oui"), "produit-24.gif", "creer.gif", false));
				} else {
					if ($connect_statut == '0minirezo') {
						echo	icone_horizontale (_T('boutique:creer_categorie'), generer_url_ecrire("categories_edit","new=oui&retour=nav"), "categorie-24.gif", "creer.gif",false);
					}
				}
			echo fin_raccourcis();
		echo debut_droite();
		
		if (spip_num_rows($res_categorie_exist) > 0 ){
			$res_liste_produits_publie = spip_query("SELECT id_produit, titre FROM spip_boutique_categories WHERE id_auteur = '".$connect_id_auteur."' AND statut='publie';");
			if (spip_num_rows($res_liste_produits_publie) > 0 ){
				$res_liste_produits_prepa = spip_query("SELECT id_produit, titre FROM spip_boutique_categories WHERE id_auteur = '".$connect_id_auteur."' AND statut='prepa';");
				$res_liste_produits_refuse = spip_query("SELECT id_produit, titre FROM spip_boutique_categories WHERE id_auteur = '".$connect_id_auteur."' AND statut='refuse';");
			}else{
				//echo _T('boutique:pas_d_article_a_cette_adresse');
				echo debut_cadre_trait_couleur();
				echo _T('boutique:creer_un_produit');
				echo fin_cadre_trait_couleur();
			}
			
		}else{
			echo debut_cadre_trait_couleur();
			echo _T('boutique:creer_categorie');
			echo fin_cadre_trait_couleur();
		}
		
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}
?>
