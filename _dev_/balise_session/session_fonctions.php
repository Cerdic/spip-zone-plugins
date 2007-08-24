<?php

function balise_SESSION_dist($p) {
	$p->descr['session'] = true;

	$f = function_exists('balise_ENV')
		? 'balise_ENV'
		: 'balise_ENV_dist';

	$p = $f($p, '$GLOBALS["auteur_session"]');
	return $p;
}


?>