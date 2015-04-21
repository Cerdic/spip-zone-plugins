<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function links_insert_head_css($flux) {
	//Recuperation de la configuration
	$links = sql_fetsel('valeur', 'spip_meta', 'nom = "links"');
	$links = unserialize($links['valeur']);
	//Styles
	if($links['style'] == 'on'){
		$flux .= '<link rel="stylesheet" href="'.find_in_path('css/links.css').'" type="text/css" media="all" />';
	}
	return $flux;
}

function links_insert_head($flux) {
	//Recuperation de la configuration
	$links = sql_fetsel('valeur', 'spip_meta', 'nom = "links"');
	$links = unserialize($links['valeur']);

	//Ouverture d'une nouvelle fenetre
	if($links['window'] == 'on'){
		$variables_links = "";
		//Ouverture d'une nouvelle fenetre sur les liens externes
		if($links['external'] == 'on'){
			$variables_links .= 'var links_site = \''.$GLOBALS['meta']['adresse_site'].'\';';
		}
		//Ouverture d'une nouvelle fenetre sur les documents (extensions a preciser)
		if(($links['download'] == 'on')&&($links['doc_list'])){
			$variables_links .= 'var links_doc = \''.$links['doc_list'].'\';';
		}
		$flux .= '
<!-- Liens explicites -->
<script type="text/javascript">var js_nouvelle_fenetre=\''._T('links:js_nouvelle_fenetre').'\';'.$variables_links.'</script>
<script id="spip_liens_explicites" src="'.find_in_path('links.js').'" type="text/javascript"></script>
<!-- // Liens explicites -->';
	}
	return $flux;
}