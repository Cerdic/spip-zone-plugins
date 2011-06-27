<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Balise unique FB modeles
 */
function balise_FBMOD_dist($p) {
	// Interpretation des arguments :
	//	- arg 1 : 'argument'
	//	- arg 2 : 'sinon'
	$_what = interprete_argument_balise(1,$p);
	$_sinon = interprete_argument_balise(2,$p);
	$p->code = isset($_what) && $_what ? 
		( isset($_sinon) && $_sinon ? "fbmod_config($_what, $_sinon)" : "fbmod_config($_what)" )
		: null;
	return $p;
}

?>