<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_linkcheck_tests_deplace_dist() {
	include_spip('inc/linkcheck_fcts');
	linkcheck_tests(true, 'deplace');
}
