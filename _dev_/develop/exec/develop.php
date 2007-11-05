<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_develop(){

	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('develop:develop'), "redacteurs", "develop");	
	}else{
		echo inc_commencer_page_dist(_T('develop:develop'), "redacteurs", "develop");
	}
	$tableau = $GLOBALS;
	arsort($tableau);
	echo debut_gauche();
	
	
		echo debut_boite_info();
			echo ('<h4>'._T('develop:voir_le_tableau').'</h4>');
			foreach($tableau as $key => $value){
				if (gettype($value) == 'array'){
					echo '<b><a href="'.generer_url_ecrire('develop_tableau',"nom=".$key).'">'.$key.'</a></b><hr />';
				}
			}
		echo fin_boite_info();
		
		//echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite();
	echo debut_droite(_T('develop:develop'));
	echo gros_titre(_T("develop:develop"));
	
	if ($GLOBALS['connect_statut'] == "0minirezo"){
		echo debut_cadre_relief();
		foreach($tableau as $key => $value){
			if (gettype($value) != 'array'){
				echo '<b>'.$key.' : </b> <textarea class="forml">'.$value.'</textarea><hr />';
			}
		}
		echo fin_cadre_relief();
		
	}else{
		echo develop_echec_autorisation();
	}

	
	echo fin_gauche();
	echo fin_page();

}


?>
