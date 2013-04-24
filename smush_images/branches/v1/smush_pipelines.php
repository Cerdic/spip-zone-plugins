<?php

function smush_post_image_filtrer($flux) {
	spip_log("SMUSH : pipeline post_image_filtrer sur :$flux","smush");
	// Verification sur l'adresse IP: ca ne peut marcher en local
	//if(!preg_match('/^((192\.168)|(10\.)|(172\.1[6-9]\.)|(172\.2[0-9]\.)|(172\.3[0-1]\.)|(127\.))/',$_SERVER["SERVER_ADDR"])){
		//charge la fonction necessaire
		$smush_image = charger_fonction('smush_image','inc');
		$smush_image($flux);
	//}else{
	//	spip_log('SMUSH : le plugin smush ne peut fonctionner en local','smush');
	//}
	return $flux;
}
?>