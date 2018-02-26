<?

function stats_data_header_prive($flux){
	$css = find_in_path('css/stats_data.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}

function stats_data_affiche_milieu($flux){
	// afficher le formulaire de configuration (activer ou desactiver les statistiques).
	if ($flux['args']['exec'] == 'statistiques_visites') {
		
		$flux['data'] = $flux['data'] ;
		
		//$flux['data'] .= recuperer_fond('prive/squelettes/inclure/configurer',
		//	array('configurer' => 'configurer_compteur'));
	}

	return $flux;
}
