<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function balise_NOISETTE_COMPILER_dist($p)
{
	// TODO : il faudrait appeler une fonction de service du plugin pour choisir si on passe l'id_noisette ou le couple
	// (id_conteneur, rang)
	$id_noisette = champ_sql('id_noisette', $p);
	$type_noisette = champ_sql('type_noisette', $p);
	$parametres = champ_sql('parametres', $p);
	$plugin = champ_sql('plugin', $p);

	// A-t-on demandé un stockage spécifique
	$stockage = interprete_argument_balise(1, $p);
	$stockage = isset($stockage) ? str_replace('\'', '"', $stockage) : '""';

	// si pas de contexte attribuer, on passe tout le contexte que l'on recoit
	// sinon, on regarde si 'aucun' ou 'env' est indique :
	// si 'aucun' => aucun contexte
	// si 'env' => tout le contexte recu.
	// $id_noisette est toujours transmis dans l'environnement
	$environnement = "array_merge(\$Pile[0],array('id_noisette' => $id_noisette))";

	$inclusion_dynamique = "\"<?php echo recuperer_fond(
		'noisettes/\".$type_noisette.\"',
		\".var_export(array_merge(unserialize($parametres), noizetier_choisir_contexte($type_noisette, $environnement, $id_noisette)),true).\",
		\".var_export(array('ajax'=>($_ajax && noizetier_noisette_ajax($type_noisette))),true).\"
	);?>\"";

	$inclusion_statique = "recuperer_fond(
		'noisettes/'.$type_noisette,
		array_merge(unserialize($parametres), noisette_contextualiser($plugin, $id_noisette, $type_noisette, $environnement, $stockage)),
		array('ajax'=>($_ajax && noizetier_noisette_ajax($type_noisette)))
	)";

	$code = "((noizetier_noisette_dynamique($type_noisette)) ? $inclusion_dynamique : $inclusion_statique)";

	$p->code = "((!$id_noisette) ? _T('zbug_champ_hors_motif', array('champ'=>'ID_NOISETTE', 'motif'=>'NOISETTES')) : $code)";
	$p->interdire_scripts = false;

	return $p;
}
