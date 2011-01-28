<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;



function balise_NOIZETIER_AFFICHER_NOISETTE_dist($p) {
	
	$id_noisette = champ_sql('id_noisette', $p);
	$noisette = champ_sql('noisette', $p);
	$parametres = champ_sql('parametres', $p);
	
	// As-ton demandé explicitement à ne pas ajaxifier ? #NOIZETIER_AFFICHER_NOISETTE{noajax}
	$_ajax = 'true';
	if (($v = interprete_argument_balise(1,$p))!==NULL)
		$_ajax = 'false';

	// si pas de contexte attribuer, on passe tout le contexte que l'on recoit
	// sinon, on regarde si 'aucun' ou 'env' est indique :
	// si 'aucun' => aucun contexte
	// si 'env' => tout le contexte recu.
	$environnement = '$Pile[0]';
	
	$p->code =  "(!$id_noisette) ? _T('zbug_champ_hors_motif', array('champ'=>'ID_NOISETTE', 'motif'=>'NOISETTES')) : recuperer_fond(
		'noisettes/'.$noisette,
		array_merge(unserialize($parametres), noizetier_choisir_contexte($noisette, $environnement)),
		array('ajax'=>($_ajax && noizetier_ajaxifier_noisette($noisette)))
	)";
		
	return $p;
    
}

?>
