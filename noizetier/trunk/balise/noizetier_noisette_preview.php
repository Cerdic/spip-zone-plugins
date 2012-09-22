<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;



function balise_NOIZETIER_NOISETTE_PREVIEW_dist($p) {
	
	$id_noisette = champ_sql('id_noisette', $p);
	$noisette = champ_sql('noisette', $p);
	$parametres = champ_sql('parametres', $p);
	
	$inclusion =  "recuperer_fond(
		'noisette_preview',
		array_merge(unserialize($parametres), array('noisette' => $noisette))
	)";
	
	$p->code =  "$inclusion";
	$p->interdire_scripts = false;
	return $p;
}

?>
