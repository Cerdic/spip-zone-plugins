<?php
//
// exec/gerer_digg.php
//

include_spip("inc/presentation");

function exec_gerer_digg(){
	echo debut_page(_T('spipdigg:spip_diggs'));
		echo debut_gauche();
			echo debut_boite_info();
				echo "Hello World !";
			echo fin_boite_info();
			echo debut_raccourcis();
				
			echo fin_raccourcis();
		echo debut_droite();
			//echo gros_titre(_T('spipdigg:spip_digg'));
			//echo "Hello World !";
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
