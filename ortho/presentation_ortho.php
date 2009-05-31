<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function Ortho_bouton_ortho($flux) {
	$boite = $flux['data'];
	$args = $flux['args'];
	$type = $args['type'];
	$id = $args['id'];
	$row = $args['row'];

	// Correction orthographique
	if ($type == 'article') {
		$js_ortho = "onclick=\"window.open(this.href, 'spip_ortho', 'scrollbars=yes, resizable=yes, width=740, height=580'); return false;\"";
		$boite .= icone_horizontale(_T('ortho_verifier'), generer_url_ecrire("articles_ortho", "id_article=$id"),
			_DIR_PLUGIN_ORTHO."images/ortho-24.gif",
			"rien.gif", false, $js_ortho);
	}

	$flux['data'] = $boite;
	return $flux;
}

?>
