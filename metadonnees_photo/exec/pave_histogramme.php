<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_pave_histogramme() {
	echo recuperer_fond("inc_histogramme_small",
		array('id_document' => _request('id_document')));
}		


?>