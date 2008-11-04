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

include_spip('inc/presentation');
include_spip('inc/notation');
include_spip('public/assembler');


function exec_notation(){
	notation_commencer_page();
	echo barre_onglets("notation", "notation");
	
	$flux = recuperer_fond('fonds/notation_recap', $contexte=array());
	echo "$flux";
	
	// Fin de la page
	echo fin_gauche(), fin_page();
}

?>
