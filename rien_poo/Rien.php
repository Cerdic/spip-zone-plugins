<?php

error_log("class Rien !!");

/**
 * définition du plugin "rien" version "classe statique"
 */
class Rien {
	/* static public */ function leFiltre($quelquechose) {
		// ne rien faire = retourner ce qu'on nous a envoye
		return $quelquechose.'<!-- rien_poo -->';
	}
}
?>
