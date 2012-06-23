<?php 

function balise_URL_MOT_ABSOLU_dist($p) {
	include_spip('balise/url_');
	$p->code = 'lire_config("multidomaines/editer_url").'.generer_generer_url('mot', $p);
	$p->interdire_scripts = false;
	return $p;
}
?>
