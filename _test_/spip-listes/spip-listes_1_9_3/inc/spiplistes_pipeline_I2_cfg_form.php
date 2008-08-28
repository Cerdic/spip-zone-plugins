<?php 

// inc/spiplistes_pipeline_insert_head.php (CP-20071019)

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	SPIP-Listes pipeline
	inc/spiplistes_pipeline_insert_head.php (CP-20071019)
	
	Nota: insert_head en cache. 
		Si modif ici, vider le cache
	
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function spiplistes_I2_cfg_form ($flux) {
    $flux .= recuperer_fond('fonds/inscription2_spip_listes');
	return ($flux);
}

?>
