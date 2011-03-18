<?php

function action_reperes_dist() {
	
	if (!autoriser('configurer')) {
		return;
	}
	
	$reperes = lire_config('reperes/points');
	if (!_request('type')) {
		if (!$reperes) {
			$reperes = array(
				'horizontal' => array(),
				'vertical' => array()
			);
		}
		include_spip('inc/json');
		header("Content-Type: text/json; charset=". $GLOBALS['meta']['charset']);
		echo json_encode($reperes);
		return;
	}

	$position = _request('type');
	if (!in_array($position, array('horizontal', 'vertical'))) {
		$position = 'horizontal';
	}
	$valeur   = _request('valeur');

	$ligne = array(
		'distance' => $valeur,
	);
	
	// modifier des reperes existants
	$id = _request('id');
	if (strlen($id)) {
		if ($id[0] == '-') {
			unset($reperes[$position][substr($id,1)]);
		} else {
			$reperes[$position][$id] = $ligne;
		}
	}
	// ajouter un repere
	else {
		$reperes[$position][] = $ligne;
	}

	// remettre les clés dans l'ordre...
	$reperes['horizontal'] = array_values($reperes['horizontal']);
	$reperes['vertical'] = array_values($reperes['vertical']);
	ecrire_config('reperes/points', $reperes);
}
?>
