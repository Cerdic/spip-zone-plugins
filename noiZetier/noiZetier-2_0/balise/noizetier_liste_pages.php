<?php

// S�curit�
if (!defined("_ECRIRE_INC_VERSION")) return;

// Cette balise renvoie le tableau de la liste des noisettes disponibles
function balise_NOIZETIER_LISTE_PAGES_dist($p) {
		$p->code = "noizetier_lister_pages()";
	return $p;
}


?>