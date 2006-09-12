<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/checklink');
include_spip('inc/distant');

function exec_test_distant() {

	// s'assurer que les tables sont crees
	checklink_install();
	
	$url = 'http://localhost/spip_dev/';
	$test = recuperer_page($url, false, false, 1048576, '', '', '',
		'2006-09-10 12:00:00',
		'ecrire/?exec=test_distant'
	);
	var_dump($test);
	if(is_null($test)){
		//page pas trouvee
		
	}elseif(is_int($test)){
		//on a surement le status, la page n'a pas bouge
		
	}else{
		//c'est donc du texte, on a la page, faut traiter
	
	}
}


?>