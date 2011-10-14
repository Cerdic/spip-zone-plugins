<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

## pour les liens, on transforme les guill « » des titres en guill “”
include _DIR_RESTREINT.'inc/lien.php';

function inc_lien() {

	// recuperer le resultat normal
	$args = func_get_args();
	$typo = call_user_func_array('inc_lien_dist', $args);

	// si on n'avait pas precise de titre, changer les guill du titre auto
	if ($args[1] === '')
		$typo = preg_replace(',&#171;(&nbsp;)?(.*?)(&nbsp;)?&#187;,S', '&#8220;\2&#8221;', $typo);

	// et hop
	return $typo;
}

?>
