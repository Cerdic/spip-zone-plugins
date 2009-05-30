<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');

function affiche_navigation_radio($script, $args, $debut, $i, $pack, $ancre, $query) {
	$nav = ($i <=0) ? '' : ("<a href='" . generer_url_ecrire($script, $args) ."'>0</a> ... |\n");

	$e = (_SPIP_AJAX === 1 );

	$n = spip_num_rows($query);

	for (;$n;$n--){

		if ($i == $pack*floor($i/$pack)) {
			if ($i == $debut)
				$nav .= "<span class='spip_medium'><b>$i</b></span> |\n";
			else {
				$h = generer_url_ecrire($script, $args . "&debut=$i");
				if ($e)	$e = "\nonclick=" . ajax_action_declencheur($h,$ancre);
				$nav .= "<a href='$h'$e>$i</a> |\n";
			}
		}
		$i ++;
	}

	$h = generer_url_ecrire($script, $args . "&debut=$i");

	if ($e)	$e = "\nonclick=" . ajax_action_declencheur($h,$ancre);

	return "$nav<a href='$h'$e>...</a> |";
}

?>