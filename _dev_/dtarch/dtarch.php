<?php

	function dtarch_insert_head($flux) {
		$flux .= '<link rel="stylesheet" href="'.find_in_path('dtarch.css').'" type="text/css" media="screen" />'."\n";
		/*$flux .= recuperer_fond('parametres/archives', array(
			'var_date' => _request('var_date'),
			'date' => _request('date'),
			'id_secteur' => _request('id_secteur'),
			'self' => _request('self')
		))."\n";*/
		$flux .= '<script src="'.find_in_path('dtarch.js').'" type="text/javascript"></script>'."\n";
		return $flux;
	}

?>
