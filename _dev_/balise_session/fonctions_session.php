<?php

function balise_SESSION_dist($p) {
	if(function_exists('balise_ENV'))
		return balise_ENV($p, '$GLOBALS["auteur_session"]');
	else
		return balise_ENV_dist($p, '$GLOBALS["auteur_session"]');
}

?>