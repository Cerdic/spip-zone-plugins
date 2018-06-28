<?php


function passe_complexe_header_prive($flux) {
	if (_request('exec') == 'auteur_edit') {
		include_spip('inc/passe_complexe');
		$flux .= passe_complexe_generer_javascript("input[name=new_pass]");
	}
	return $flux;
}

function passe_complexe_insert_head($flux) {
	if (_request('p')) {
		include_spip('inc/passe_complexe');
		$flux .= passe_complexe_generer_javascript("input#oubli");
	}
	return $flux;
}
