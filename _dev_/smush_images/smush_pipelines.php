<?php

function smush_post_ecrire_fichier($flux) {

    spip_log('SMUSH : pipeline post transformation','smush');
    
    $chemin = $flux['args']['chemin'];
    if(preg_match(',^image_,',$flux['args']['action'])){
		spip_log('SMUSH : pipeline post transformation sur :'.$chemin,'smush');
		//charge les fonctions necessaire
		$smush_image = charger_fonction('smush_image','inc');
		$smush_image($chemin);
    }
	return $flux;
}
?>