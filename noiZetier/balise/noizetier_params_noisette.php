<?php

// S�curit�
if (!defined("_ECRIRE_INC_VERSION")) return;

// Cette balise renvoie le tableau des param�tres d'une noizette
function balise_NOIZETIER_PARAMS_NOISETTE_dist($p) {
		$noisette = interprete_argument_balise (1, $p);
		$p->code = "noizetier_charger_parametres_noisette($noisette)";
	return $p;
}


?>