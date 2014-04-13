<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_medias_reparer_documents_dist ($t) {
	include_spip('medias_nettoyage_fonctions');

	if (function_exists('medias_reparer_documents_fichiers')) {
		medias_reparer_documents_fichiers();
	}

	return 1;
}
?>