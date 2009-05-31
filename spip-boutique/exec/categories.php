<?php
//
//	categories_page.php
//

function exec_categories(){
	global $connect_statut, $connect_id_auteur;
	echo debut_page(_T('boutique:gerer_les_categories'));
		echo debut_gauche();
			echo pipeline('affiche_gauche',array('args'=>array('exec'=>'categories_page'),'data'=>''));
			echo debut_boite_info();
				echo _T('boutique:gerer_les_categories');
			echo fin_boite_info();
			//echo debut_raccourcis();
				//echo bloc_des_raccourcis(icone_horizontale(_T('boutique:icone_creer_categorie'), generer_url_ecrire("categorie_edit","id_categorie=new"), _DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png", "creer.gif", false));
			//echo fin_raccourcis();
		echo debut_droite();
		$res_categorie = spip_query("SELECT * FROM spip_boutique_categories WHERE id_categorie='"._request('id_categorie')."';");
		$categorie = spip_fetch_array($res_categorie); 
		if (spip_num_rows($res_categorie) > 0 ){
			echo debut_cadre_trait_couleur(_DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png", '');
			echo '<div class="verdana2 spip_large"><b>'.$categorie['titre'].'</b></div><br />';
			echo '<div style="border: 1px dashed rgb(170, 170, 170); padding: 5px;" class="verdana1 spip_small" align="left">'.propre($categorie['descriptif']).'</div><br />';
			echo icone_horizontale ( _T('boutique:modifier_la_categorie'), generer_url_ecrire("categorie_edit","id_categorie="._request('id_categorie')), _DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png", "edit.gif", false);
			echo fin_cadre_trait_couleur(true);
			
			
			$res_liste_sous_categories = spip_query("SELECT id_categorie, titre, descriptif FROM spip_boutique_categories WHERE id_parent = '"._request('id_categorie')."';");
			while ($sous_categorie = spip_fetch_array($res_liste_sous_categories)){
				echo debut_cadre_sous_rub ( _DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png", false, "", "" );
					echo  '<span dir="ltr"><b><a href="'.lire_meta('adresse_site').'/ecrire/?exec=categories&amp;id_categorie='.$sous_categorie['id_categorie'].'">'.$sous_categorie['titre'].'</b></a></span><br /><div class="verdana1">'.$sous_categorie['descriptif'].'</div>';
				echo fin_cadre_sous_rub (false);
			}
			
			echo icone_horizontale(_T('boutique:icone_creer_categorie'), generer_url_ecrire("categorie_edit","id_categorie=new&id_parent="._request('id_categorie')), _DIR_PLUGIN_BOUTIQUE."img_pack/caissons_categorie.png", "creer.gif", false);
			
			
			$res_liste_produits_publie = spip_query("SELECT id_produit, titre FROM spip_boutique_produits WHERE id_auteur = '".$connect_id_auteur."' AND statut='publie' AND id_categorie='"._request('id_categorie')."';");
			while ($produits_publie = spip_fetch_array($res_liste_produits_publie)){
				echo debut_cadre_formulaire();
				echo '<a href="?exec=produits&amp;id_produit='.$produits_publie[id_produit].'">'.$produits_publie['titre'].'</a>';
				echo fin_cadre_formulaire();
			}
			$res_liste_produits_prepa = spip_query("SELECT id_produit, titre FROM spip_boutique_produits WHERE id_auteur = '".$connect_id_auteur."' AND statut='prepa' AND id_categorie='"._request('id_categorie')."';");
			$res_liste_produits_refuse = spip_query("SELECT id_produit, titre FROM spip_boutique_produits WHERE id_auteur = '".$connect_id_auteur."' AND statut='refuse' AND id_categorie='"._request('id_categorie')."';");
			echo icone_horizontale (_T('boutique:creer_produit'), generer_url_ecrire("produit_edit","id_produit=new&id_parent="._request('id_categorie')), _DIR_PLUGIN_BOUTIQUE."img_pack/petite_caisse.png", "creer.gif",false);
			
			
		}else{
			echo _T('boutique:pas_de_categorie_a_cette_adresse');
		}
		
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}
?>
