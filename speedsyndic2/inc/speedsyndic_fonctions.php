<?php
function traiter_site($id_syndic) {
	$address=include_spip('genie/syndic');
	define('_GENIE_SYNDIC', 2); // Pas de faux message d'erreur
	$t = syndic_a_jour($id_syndic);
	return $t ? 0 : $id_syndic;
}
?>