<?php


function exec_en_travaux(){
include_ecrire("inc_presentation");

 
	debut_page("En travaux");
	
	echo "<br /><br /><br />";
	gros_titre("En travaux");
	debut_gauche();
	
	debut_boite_info();
	echo propre("Cette page permet de mettre un message temporaire sur toute les pages du site pendant une phase de maintenance.");	
	fin_boite_info();
	
	debut_droite();
	  
	echo "<span style='font:Georgia,Garamond,Times,serif;font-size:medium'>";
	 
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		echo "<strong>Paramétrage page travaux : </strong>";
	}
	else 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>";
	echo "</span>";
}

?>