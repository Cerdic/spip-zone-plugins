<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_medias_deplacer_orphelins_dist ($t) {

	medias_deplacer_documents_repertoire_orphelins();

	return 1;
}
?>