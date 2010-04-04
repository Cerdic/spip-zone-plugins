<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Cette balise renvoie le tableau des paramètres d'une noizette
function balise_NOIZETIER_PARAMS_NOISETTE_dist($p) {
		$noisette = interprete_argument_balise (1, $p);
		$p->code = "noizetier_charger_parametres_noisette($noisette)";
	return $p;
}


?>