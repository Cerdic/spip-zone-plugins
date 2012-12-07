<?php
function dida_install($action){

	switch ($action){
	
	case 'install':
	if (!@opendir(_DIR_IMG."didapages")) {mkdir(_DIR_IMG."didapages");}
	break;
	
	case 'test':
	if (!@opendir(_DIR_IMG."didapages")) {mkdir(_DIR_IMG."didapages");}
	return true;
	break;
       
	case 'uninstall':
	break;
	}
}
?>