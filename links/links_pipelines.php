<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function links_configuration() {
	$links = isset($GLOBALS['meta']['links']) ? unserialize($GLOBALS['meta']['links']) : array(
		'style' => 'off',
		'external' => 'off',
		'download' => 'off',
		'window' => 'off',
		'doc_list' => ".pdf,.ppt,.xls,.doc",
	);
	return $links;
}

function links_insert_head_css($flux) {
	//Recuperation de la configuration
	$links = links_configuration();

	//Styles
	if($links['style'] == 'on'){
		$flux .= '<link rel="stylesheet" href="'.find_in_path('css/links.css').'" type="text/css" media="all" />';
	}
	//Ouverture d'une nouvelle fenetre : insertion des init js inline, en amont des CSS (perf issue)
	if($links['window'] == 'on'){
		$js = 'var js_nouvelle_fenetre=\''._T('links:js_nouvelle_fenetre').'\';';
		//Ouverture dune nouvelel fenetre sur les liens externes
		if($links['external'] == 'on'){
			$js .= 'var links_site = \''.$GLOBALS['meta']['adresse_site'].'\';';
		}
		//Ouverture d'une nouvelle fenetre sur les documents (extensions a preciser)
		if(($links['download'] == 'on')&&($links['doc_list'])){
			$js .= 'var links_doc = \''.$links['doc_list'].'\';';
		}
		$flux = '<script type="text/javascript">'.$js.'</script>' . "\n" . $flux;
	}

	return $flux;
}

function links_insert_head($flux) {
	//Recuperation de la configuration
	$links = links_configuration();

	//Ouverture d'une nouvelle fenetre
	if($links['window'] == 'on'){
		$flux .= '<script src="'.find_in_path('links.js').'" type="text/javascript"></script>'. "\n";
	}
	return $flux;
}
