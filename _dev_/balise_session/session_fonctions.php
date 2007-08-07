<?php

function balise_SESSION_dist($p) {
	$GLOBALS['squelettes_a_sessions'][$p->descr['nom']] = true;

	$f = function_exists('balise_ENV')
		? 'balise_ENV'
		: 'balise_ENV_dist';

	$p = $f($p, '$GLOBALS["auteur_session"]');
	return $p;
}

?>