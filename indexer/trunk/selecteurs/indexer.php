<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function selecteurs_indexer_dist() {
	$search = _request('q');
	$max_terms = 10;
	$auto = array();
	$dossier_dict = _DIR_IMG . 'indexer_autocomplete/';
	
	if (strlen($search) >= 2) {
		include_spip('inc/charsets');
		$racine = strtolower(translitteration($search));
		$ab = mb_substr($racine, 0, 2);
		$a = mb_substr($ab, 0, 1);
		$f = $dossier_dict . $a . '/' . $ab . '.txt';
		
		if (!file_exists($f)) {
			var_dump($f);
			return;
		}
		
		$mots = array_map('trim', file($f));
		foreach($mots as $m) {
			$m1 = strtolower(translitteration($m));
			if (
				substr($m1, 0, strlen($racine)) == $racine
				and strlen($m1) > strlen($racine)
			) {
				$auto[] = $m;
				
				if (count($auto) >= $max_terms) {
					break;
				}
			}
		}
	}
	// retrier les résultats par ordre alphabétique
	ksort($auto);
	return json_encode(array_values($auto));
}
