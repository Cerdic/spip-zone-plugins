<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_pave_histogramme() {
	include_spip('inc/actions');
	ajax_retour(recuperer_fond("prive/squelettes/inclure/image_histogramme", array('max'=>138,'id_document' => _request('id_document'))));
}


?>