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
 * La signature de la balise est : `#NOISETTE_COMPILER{id_noisette[, stockage]}`.
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
	$est_conteneur = champ_sql('est_conteneur', $p);
	$parametres = champ_sql('parametres', $p);

	// Plugin et éventuel stockage spécifique
	$plugin = champ_sql('plugin', $p);
	$stockage = interprete_argument_balise(2, $p);
	$stockage = isset($stockage) ? str_replace('\'', '"', $stockage) : '""';

	// Cas d'une noisette conteneur : 
	// - on ne compile pas la noisette conteneur mais on appelle la compilation des noisettes incluses (récursif),
	// - et on applique systématiquement une encapsulation avec comme capsule la noisette conteneur elle-même.
	// L'appel du fond conteneur_compiler pour le noisettes incluses est non ajaxé et l'environnement n'est pas fourni
	// (seules les variables nécessaires à la détermination des noisettes incluses, à savoir, l'id du conteneur, 
	// le plugin et le stockage sont passées).
	// Seule l'inclusion statique est possible pour l'appel à la compilation des noisettes incluses.
	// L'encapsulation se fait en compilant la noisette conteneur avec ses paramètres et sans ajax.
	$inclusion_statique_conteneur = "noisette_encapsuler(
		$plugin,
		recuperer_fond(
			'conteneur_compiler',
			array(
				'plugin'=>$plugin,
				'id_conteneur'=>calculer_identifiant_conteneur($plugin, $id_noisette, $type_noisette, $stockage),
				'stockage'=>$stockage
			),
			array()
		),
		'conteneur',
		array_merge(unserialize($parametres), array('type_noisette' => $type_noisette)),
		$stockage
	)";

	// Cas d'une noisette 'non conteneur' : 
	// - on compile la noisette,
	// - et on appelle l'encapsulation avec ses paramètres adéquates configurés pour la noisette (encapsulation, css, type)
	$environnement = "\$Pile[0]";
	$encapsulation = champ_sql('encapsulation', $p);
	$css = champ_sql('css', $p);
	$inclusion_dynamique_noisette = "\"<?php echo noisette_encapsuler(
		\".$plugin.\",
		recuperer_fond(
			\".type_noisette_localiser($plugin, $type_noisette).\",
			\".var_export(array_merge(unserialize($parametres), noisette_contextualiser($plugin, $noisette, $type_noisette, $environnement, $stockage)),true).\",
			\".var_export(array('ajax'=>(type_noisette_ajaxifier($plugin, $type_noisette, $stockage))),true).\"
		),
		\".$encapsulation.\",
		\".var_export(array('id_noisette' => $id_noisette, 'type_noisette' => $type_noisette, 'css' => $css)),true).\",
		\".$stockage.\",
	);?>\"";
	$inclusion_statique_noisette = "noisette_encapsuler(
		$plugin,
		recuperer_fond(
			type_noisette_localiser($plugin, $type_noisette),
			array_merge(unserialize($parametres), noisette_contextualiser($plugin, $noisette, $type_noisette, $environnement, $stockage)),
			array('ajax'=>(type_noisette_ajaxifier($plugin, $type_noisette, $stockage)))
		),
		$encapsulation,
		array('id_noisette' => $id_noisette, 'type_noisette' => $type_noisette, 'css' => $css),
		$stockage
	)";

	// Finaliser le code en choisissant le type d'inclusion. La fonction type_noisette_dynamiser() renvoie toujours
	// false pour une noisette conteneur.
	$code = "($est_conteneur == 'oui'
		? $inclusion_statique_conteneur
		: (type_noisette_dynamiser($plugin, $type_noisette, $stockage) 
			? $inclusion_dynamique_noisette 
			: $inclusion_statique_noisette))";
	$p->code = "((!$id_noisette) ? _T('zbug_champ_hors_motif', array('champ'=>'ID_NOISETTE', 'motif'=>'NOISETTES')) : $code)";
	$p->interdire_scripts = false;

	return $p;
}


function calculer_identifiant_conteneur($plugin, $id_noisette, $type_noisette, $stockage) {

	include_spip('ncore/ncore');
	$id_conteneur = ncore_conteneur_identifier(
		$plugin,
		array('type_noisette' => $type_noisette, 'id_noisette' => $id_noisette),
		$stockage
	);

	return $id_conteneur;
}
