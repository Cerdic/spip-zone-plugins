<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_medias_deplacer_documents_orphelins_dist ($t) {

	include_spip('medias_nettoyage_fonctions');

	medias_deplacer_documents_repertoire_orphelins();
	return 1;
}
?>