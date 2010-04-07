<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_NOIZETIER_AFFICHER_NOISETTE_dist($p) {
	
	$id_noisette = champ_sql('id_noisette', $p);
	$noisette = champ_sql('noisette', $p);
	$parametres = champ_sql('parametres', $p);
	
	$p->code =  "(!$id_noisette) ? _T('zbug_champ_hors_motif', array('champ'=>'ID_NOISETTE', 'motif'=>'NOISETTES')) : recuperer_fond(
		'noisettes/'.$noisette,
		array_merge(unserialize($parametres), \$Pile[0]),
		array('ajax'=>true)
	)";
	
	return $p;
    
}

?>
