<?php

function outils_jai_de_la_chance_config_dist() {

	$action = generer_form_action('jai_de_la_chance',
		"<input type='submit' value='".attribut_html(_T("blagoulames:jai_de_la_chance_bouton"))."' />");
	$action = str_replace("\n", "", $action);

	$action_publique = generer_form_action('jai_de_la_chance',
		"<input type='submit' value='".attribut_html(_T("blagoulames:jai_de_la_chance_bouton"))."' />",
		'', true);
	$action_publique = str_replace("\n", "", $action_publique);	

	add_outil(array(
		'id'          => "jai_de_la_chance",
		'nom'         => _T("blagoulames:jai_de_la_chance_nom"),
		'description' => _T("blagoulames:jai_de_la_chance_description"),
		'categorie'   => _T('blagoulames:categorie'),
		'code:jq'     => "
			action = \"$action_publique\";
			if (cs_prive) {
				action = \"$action\";
			}
			jQuery(\"<div id='chance'></div>\")
			.html(action)
			.css({
				'right': '10px',
				'top': '10px',
				'position': 'absolute',
				'z-index': '100'})
			.prependTo(jQuery('body'));
		"
	));	
	
}
?>
