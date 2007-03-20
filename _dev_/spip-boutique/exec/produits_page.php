<?php
//
//	produits_page.php
//

function exec_produits_page(){
	global $connect_statut, $connect_id_auteur;
	echo debut_page(_T('boutique:gerer_les_produits'));
		echo debut_gauche();
			echo pipeline('affiche_gauche',array('args'=>array('exec'=>'produits_page'),'data'=>''));
			echo debut_boite_info();
				echo _T('boutique:gerer_les_produits');
			echo fin_boite_info();
			echo debut_raccourcis();
				$result = spip_query("SELECT id_categorie FROM spip_boutique_categories LIMIT 1");
				if (spip_num_rows($result) > 0) {
				echo bloc_des_raccourcis(icone_horizontale(_T('boutique:icone_creer_produit'), generer_url_ecrire("produits_edit","new=oui"), "produit-24.gif", "creer.gif", false));
				} else {
					if ($connect_statut == '0minirezo') {
						echo _T('boutique:creer_categorie');
						echo	bloc_des_raccourcis(icone_horizontale (_T('boutique:creer_categorie'), generer_url_ecrire("categories_edit","new=oui&retour=nav"), "categorie-24.gif", "creer.gif",false));
				}
			echo fin_raccourcis();
		echo debut_droite();
		
		if (spip_num_rows($result) > 0 ){
			$res_liste_produits_publie = spip_query("SELECT id_produit, titre FROM spip_boutique_categories WHERE id_auteur = '".$connect_id_auteur."' AND etat='publie';");
			if (spip_num_rows($res_liste_produits_publie) > 0 ){
				
			}
			$res_liste_produits_prepa = spip_query("SELECT id_produit, titre FROM spip_boutique_categories WHERE id_auteur = '".$connect_id_auteur."' AND etat='prepa';");
			$res_liste_produits_refuse = spip_query("SELECT id_produit, titre FROM spip_boutique_categories WHERE id_auteur = '".$connect_id_auteur."' AND etat='refuse';");
			
		}
		
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}
?>
