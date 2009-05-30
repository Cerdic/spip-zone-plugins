<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@balise_URL_DOCUMENT_dist
function balise_URL_DOCUMENT_dist($p) {
	if (!isset($GLOBALS['dw2_param']) or !is_array($GLOBALS['dw2_param'])) {
		include_spip('inc/dw2_lireconfig');
		lire_dw2_config();
	}
	if (isset($GLOBALS['dw2_param']['forcer_url_dw2']) AND $GLOBALS['dw2_param']['forcer_url_dw2']=="oui") {
		return balise_URL_DOC_OUT($p);
	} else {
		$_id_document = interprete_argument_balise(1,$p);
		$s = $p->id_boucle;
		$type='document';
		if (!$_id_document)
			$_id_document = champ_sql('id_document',$p);
		//$p->code = "generer_url_document($_id_document)";
		$p->code = "generer_url_entite($_id_document, '$type')";

		$p->interdire_scripts = false;
		return $p;
		}
}
?>