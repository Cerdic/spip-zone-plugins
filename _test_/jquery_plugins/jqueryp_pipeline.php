<?php


// ajoute les plugins jquery dans jquery.js.html
function jqueryp_insert_jquery_plugins($texte) {
	$lpa = jqueryp_liste_plugins_actifs();
	$texte .= jqueryp_add_plugins(array_values(array_flip($lpa)));
	return $texte;
}

?>
