<?php
//
// exec/admin_digg.php
//

include_spip("inc/presentation");

function exec_admin_digg(){
		echo debut_page(_T('spipdigg:spip_diggs'));
		echo debut_gauche();
			echo debut_boite_info();
				echo "Hello World !";
			echo fin_boite_info();
			//~ echo debut_raccourcis();
				//~ echo '<a href="?exec=editer_digg&new=oui">'._T('spipdigg:ajouter_des_digg').'</a>';
			//~ echo fin_raccourcis();
		echo debut_droite();
			debut_cadre_trait_couleur();
			echo'config des digg';
			fin_cadre_trait_couleur();
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
