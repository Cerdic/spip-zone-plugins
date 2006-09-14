<?php

include_spip('inc/checklink');
include_spip('public/assembler');

function exec_liens_tous(){
  include_spip("inc/presentation");

  checklink_verifier_base();
	
	debut_page(_L("Tous les liens"), "documents", "liens");
	debut_gauche();
	//debut_boite_info();
	//echo _L("Cliquez sur un formulaire pour le modifier ou le visualiser avant suppression.");
	//fin_boite_info();
	
	debut_droite();
	
	echo recuperer_fond("exec/table_liens",array());
	
	echo "<br />\n";
	

	
	fin_page();
}

?>