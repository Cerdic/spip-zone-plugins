<?php
/**
* Plugin Notation
* par JEM (jean-marc.viglino@ign.fr) / b_b
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/notation');

function exec_notation_param(){
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		minipres();
	}
	
	// Afficher les menus
	notation_commencer_page();
	echo barre_onglets("notation", "notation_param");
	
	// configs CFG
	include_spip("public/assembler");
	echo recuperer_fond("fonds/notation_config");

	echo fin_gauche(), fin_page();
	
}
?>
