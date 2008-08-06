<?php
/******************************************************************
***  Ce plugin eva_freemind, crיי par Olivier Gautier et ***
*** Dominique Lepaisan est mis א disposition sous un ***
*** contrat GNU/GPL consultable א l'adresse                    ***
***      http://www.april.org/gnu/gpl_french.html		     ***
******************************************************************/
function eva_freemind_install($action){
	
	switch ($action){
	
	case 'test':
	$test_req=sql_select('inclus','spip_types_documents',"extension = 'mm'");
	$test_ta=sql_fetch($test_req);
	$test=$test_ta['inclus'];
	if ((!@opendir(_DIR_IMG."icones")) OR (!@fopen(_DIR_IMG."icones/mm.png", "r")) OR !$test) {return false;}
	else {return true;}
	break;

	case 'install':
	if (!@opendir(_DIR_IMG."icones")) {mkdir(_DIR_IMG."icones");}
	if (!@fopen(_DIR_IMG."icones/mm.png", "r")) {copy(_DIR_PLUGIN_EVA_FREEMIND.'img_pack/mm.png',_DIR_IMG.'icones/mm.png');}
	$test_req=sql_select('inclus','spip_types_documents',"extension = 'mm'");
	$test_ta=sql_fetch($test_req);
	$test=$test_ta['inclus'];
	if (!$test) {
	sql_insertq('spip_types_documents',array('extension' => 'mm','mime_type' => 'application/x-freemind','titre' => 'Freemind','inclus' => 'embed','upload' => 'oui'));
	}
	break;
       
	case 'uninstall':
	break;
	}
}
?>