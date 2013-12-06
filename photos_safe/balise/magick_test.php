<?php

function balise_MAGICK_TEST($p){
	$p->type = 'php';
	return calculer_balise_dynamique($p, 'MAGICK_TEST', array());
}


function balise_MAGICK_TEST_dyn(){
	if (photosafe_ext_test()){return 'true';}
	return 'false'; 
}

?>

