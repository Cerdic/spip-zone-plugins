<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion de la boussole courante dans la liste des boussoles pouvant être fournies par le serveur
 *
 * @param object $flux
 * @return $flux
 */
function bouspip_declarer_boussoles($flux) {
	$flux['bouspip'] = "spip";

	return $flux;
}

?>
