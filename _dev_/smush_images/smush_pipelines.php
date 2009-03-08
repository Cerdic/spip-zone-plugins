<?php

function smush_post_image_filtrer($flux) {
	spip_log('SMUSH : pipeline post_image_filtrer sur :'.$flux,'smush');
	
	//charge la fonction necessaire
	$smush_image = charger_fonction('smush_image','inc');
	$smush_image($flux);
	
	return $flux;
}
?>