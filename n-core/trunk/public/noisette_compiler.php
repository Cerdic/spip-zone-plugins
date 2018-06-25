<?php
/**
 * Ce fichier contient la balise `#NOISETTE_COMPILER` qui génère l'affichage public d'une noisette.
 *
 * @package SPIP\NCORE\NOISETTE\BALISE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compile la balise `#NOISETTE_COMPILER` qui génère l'affichage de la noisette passée en argument.
 * La signature de la balise est : `#CONTENEUR_IDENTIFIER{id_noisette[, stockage]}`.
 *
 * @balise
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_NOISETTE_COMPILER_dist($p) {

	// On passe dans le contexte toujours les deux identifiants d'une noisette, à savoir, l'id_noisette et le couple
	// (id_conteneur, rang).
	$id_noisette = interprete_argument_balise(1, $p);
	$id_noisette = isset($id_noisette) ? $id_noisette : '0';

	$id_conteneur = champ_sql('id_conteneur', $p);
	$rang_noisette = champ_sql('rang_noisette', $p);
	$noisette = "array(
		'id_noisette' => $id_noisette,
		'id_conteneur' => $id_conteneur,
		'rang_noisette' => $rang_noisette
	)";

	// On extrait les autres informations de la noisette
	$type_noisette = champ_sql('type_noisette', $p);
	$parametres = champ_sql('parametres', $p);
	$plugin = champ_sql('plugin', $p);

	// A-t-on demandé un stockage spécifique en paramètre de la balise ?
	$stockage = interprete_argument_balise(2, $p);
	$stockage = isset($stockage) ? str_replace('\'', '"', $stockage) : '""';

	// On récupère l'environnement
	$environnement = "\$Pile[0]";

	// On prépare le code en fonction du type d'inclusion dynamique ou pas
	$inclusion_dynamique = "\"<?php echo recuperer_fond(
		\".type_noisette_localiser($plugin, $type_noisette).\",
		\".var_export(array_merge(unserialize($parametres), noisette_contextualiser($plugin, $noisette, $type_noisette, $environnement, $stockage)),true).\",
		\".var_export(array('ajax'=>(type_noisette_ajaxifier($plugin, $type_noisette, $stockage))),true).\"
	);?>\"";
	$inclusion_statique = "recuperer_fond(
		type_noisette_localiser($plugin, $type_noisette),
		array_merge(unserialize($parametres), noisette_contextualiser($plugin, $noisette, $type_noisette, $environnement, $stockage)),
		array('ajax'=>(type_noisette_ajaxifier($plugin, $type_noisette, $stockage)))
	)";
	$code = "((type_noisette_dynamiser($plugin, $type_noisette, $stockage)) ? $inclusion_dynamique : $inclusion_statique)";

	$p->code = "((!$id_noisette) ? _T('zbug_champ_hors_motif', array('champ'=>'ID_NOISETTE', 'motif'=>'NOISETTES')) : $code)";
	$p->interdire_scripts = false;

	return $p;
}
