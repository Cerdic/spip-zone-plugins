<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;



function balise_NOIZETIER_AFFICHER_NOISETTE_dist($p) {
	
	$id_noisette = champ_sql('id_noisette', $p);
	$noisette = champ_sql('noisette', $p);
	$parametres = champ_sql('parametres', $p);
	
	// As-t-on demandé explicitement à ne pas ajaxifier ? #NOIZETIER_AFFICHER_NOISETTE{noajax}
	$_ajax = 'true';
	if (($v = interprete_argument_balise(1,$p))!==NULL)
		$_ajax = 'false';
		

	// si pas de contexte attribuer, on passe tout le contexte que l'on recoit
	// sinon, on regarde si 'aucun' ou 'env' est indique :
	// si 'aucun' => aucun contexte
	// si 'env' => tout le contexte recu.
	// $id_noisette est toujours transmis dans l'environnement
	$environnement = "array_merge(\$Pile[0],array('id_noisette' => $id_noisette))";
	
	$inclusion_dynamique = "\"<?php echo recuperer_fond(
		'noisettes/\".$noisette.\"',
		\".var_export(array_merge(unserialize($parametres), noizetier_choisir_contexte($noisette, $environnement, $id_noisette)),true).\",
		\".var_export(array('ajax'=>($_ajax && noizetier_ajaxifier_noisette($noisette))),true).\"
	);?>\"";

	$inclusion_statique =  "recuperer_fond(
		'noisettes/'.$noisette,
		array_merge(unserialize($parametres), noizetier_choisir_contexte($noisette, $environnement, $id_noisette)),
		array('ajax'=>($_ajax && noizetier_ajaxifier_noisette($noisette)))
	)";
	
	$code = "((noizetier_inclusion_dynamique($noisette)) ? $inclusion_dynamique : $inclusion_statique)";
	
	$p->code =  "((!$id_noisette) ? _T('zbug_champ_hors_motif', array('champ'=>'ID_NOISETTE', 'motif'=>'NOISETTES')) : $code)";
	$p->interdire_scripts = false;
	
	return $p;
    
}
