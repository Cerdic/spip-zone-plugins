<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Cette balise renvoie le tableau de la liste des noisettes disponibles
function balise_NOIZETIER_LISTE_NOISETTES_dist($p) {
		$p->code = "noizetier_lister_noisettes()";
	return $p;
}


?>