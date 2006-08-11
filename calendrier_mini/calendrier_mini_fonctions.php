<?php

function balise_DATE_ARCHIVES($p, $var_date = 'archives') {
	$p->code = "_request('".$var_date."')";

	#$p->interdire_scripts = true;
	return $p;
}

?>
