<?php
/**
* Plugin Notation
* par JEM (jean-marc.viglino@ign.fr) / b_b
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Affichage de la page principale
*  
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/vieilles_defs');
include_spip('inc/presentation');
include_spip('inc/notation_menu');
include_spip('public/assembler');

function petit_titre($titre){
	global $couleur_foncee;
	echo "<div class='verdana3' style='color:$couleur_foncee; font-weight:bold'>".$titre."</div><hr style='color:$couleur_foncee;height:1px' />\n";
}

function exec_notation(){
	if (ecrire_menu()){
		$flux = recuperer_fond('fonds/notation_recap', $contexte=array());
		echo "$flux";
	}
	// Fin de la page
	echo fin_gauche(), fin_page();
}

?>