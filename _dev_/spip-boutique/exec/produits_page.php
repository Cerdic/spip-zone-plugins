<?php
//
//	produits_page.php
//

function exec_produits_page(){
	echo debut_page(_T('boutique:gerer_les_produits'));
		echo debut_gauche();
			echo debut_boite_info();
				echo _T('boutique:gerer_les_produits');
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=produits_edit&id_produit=new">'._T('boutique:ajouter_un_produit').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
		
		if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
		echo fin_page();
}
?>
