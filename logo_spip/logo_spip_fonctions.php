<?php

function balise_LOGO_SPIP($p) {
	$p->code="filtrer('balise_img', find_in_path('spip.png'), 'SPIP', 'spip_logo')";
	return $p;
}