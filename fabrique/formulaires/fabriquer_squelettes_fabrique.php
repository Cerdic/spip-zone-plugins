<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_fabriquer_squelettes_fabrique_charger_dist(){
	return array(
		'code_squelette' => '',
		'code_resultat' => '',
		'echappements' => array(
			'php', 'crochets', 'diese', 'tag_boucle', 'idiome', 'inclure'
		)
	);
}


function formulaires_fabriquer_squelettes_fabrique_traiter_dist(){

	$echappements = _request('echappements');
	$source = _request('code_squelette');
	$echap = array(
		'diese'       => array('#' => '\#'),
		'crochets'    => array('[' => '\[', ']' => '\]'),
		'parentheses' => array('(' => '\(', ')' => '\)'),
		'accolades'   => array('{' => '\{', '}' => '\}'),
		'php'         => array('<?php' => '#PHP'), // doit être apres le \# et avant le \< 
		'tag_boucle'  => array('<B' => '\<B', '</B'=>'\</B', '<//B'=>'\<//B'),
		'inclure'     => array('<INCLURE' => '\<INCLURE'),
		'idiome'      => array('<:' => '\<:'),
	);

	// 1) Échapper les échappements du squelette.
	$chercher = array();
	$remplacer = array();

	foreach ($echap as $e) {
		foreach($e as $cherche => $remplace) {
			$chercher[] = $remplacer;
			$remplacer[] = '\\' . $remplace;
		}
	}
	$skel = str_replace($chercher, $remplacer, $source);

	// 2) Échapper les caractères de SPIP demandes


	// on ne garde que ceux demandes
	$echap = array_intersect_key($echap, array_flip($echappements));
	$chercher = array();
	$remplacer = array();

	foreach ($echap as $e) {
		foreach($e as $cherche => $remplace) {
			$chercher[] = $cherche;
			$remplacer[] = $remplace;
		}
	}

	// on remplace.
	$skel = str_replace($chercher, $remplacer, $skel);
	
	set_request('code_resultat', $skel);

	$res = array(
		'editable'=>'oui',
		'message_ok' => _T('fabrique:calcul_effectue'),
	);
	return $res;
}



?>
