<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function photo_infos_afficher_metas_document($flux){
	if ($id_document = $flux['args']['id_document']){
		$flux["data"] .= recuperer_fond("pave_exif",array('id_document' => $id_document));
	}
	return $flux;
}
?>
