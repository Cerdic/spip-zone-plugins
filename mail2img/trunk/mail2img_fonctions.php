<?php
/**
 * Plugin mail2img
 * (c) 2012 cy_altern
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// recupere la partie de l'adresse mail avant le @ pour generer un nom
function mail2img_recup_nom($mail) {
	if (strpos($mail, '@') === FALSE)
		return FALSE;
	$Tfrom = explode('@', $mail);
	return str_replace(array('.','_'), ' ', $Tfrom[0]);
}
?>
