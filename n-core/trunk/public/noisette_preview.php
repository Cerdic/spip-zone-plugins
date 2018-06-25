<?php
/**
 * Ce fichier contient la balise `#NOISETTE_PREVIEW` qui génère la prévisualisation d'une noisette.
 *
 * @package SPIP\NCORE\NOISETTE\BALISE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compile la balise `#NOISETTE_PREVIEW` qui génère la prévisualisation de la noisette passée en argument
 * en gérant le fait que le type de noisette soit actif et propose bien un fichier de prévisualisation.
 * La signature de la balise est : `#NOISETTE_PREVIEW{id_noisette, type_noisette_actif, plugins_necessites}`.
 *
 * @package SPIP\NCORE\NOISETTE\BALISE
 * @balise
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_NOISETTE_PREVIEW_dist($p) {

	// On passe dans le contexte toujours les deux identifiants d'une noisette, à savoir, l'id_noisette et le couple
	// (id_conteneur, rang).
	// -- L'id de la noisette qui est passé en argument.
	$id_noisette = interprete_argument_balise(1, $p);
	$id_noisette = isset($id_noisette) ? str_replace('\'', '"', $id_noisette) : '0';
	// -- L'autre identifiant de la noisette, à savoir, le couple (id_conteneur, rang) qui est lu dans la pile.
	$id_conteneur = champ_sql('id_conteneur', $p);
	$rang_noisette = champ_sql('rang_noisette', $p);
	// -- Le type et les paramètres qui sont lus dans la pile.
	$type_noisette = champ_sql('type_noisette', $p);
	$parametres = champ_sql('parametres', $p);
	$noisette = "array(
		'id_noisette' => $id_noisette,
		'id_conteneur' => $id_conteneur,
		'rang_noisette' => $rang_noisette,
		'type_noisette' => $type_noisette,
	)";

	// On identifie si le type de noisette est actif ou pas afin de faire appel au bon fond.
	$type_noisette_actif = interprete_argument_balise(2, $p);
	$type_noisette_actif = isset($type_noisette_actif) ? str_replace('\'', '"', $type_noisette_actif) : '"oui"';

	// On récupère le tableau des necessite du type de noisette afin d'afficher les plugins inactifs si besoin.
	$type_noisette_necessite = interprete_argument_balise(3, $p);
	$type_noisette_necessite = isset($type_noisette_necessite) ? str_replace('\'', '"', $type_noisette_necessite) : '"a:0:{}"';

	// On récupère aussi le plugin appelant qui fait partie du stockage de la noisette.
	$plugin = champ_sql('plugin', $p);

	// On appelle la fonction de calcul de la prévisualisation.
	$p->code = "calculer_preview_noisette(
		$plugin, 
		array_merge($noisette, unserialize($parametres)), 
		$type_noisette_actif,
		$type_noisette_necessite)";
	$p->interdire_scripts = false;

	return $p;
}

/**
 * Calcule la prévisualisation de la noisette passée en argument.
 *
 * @uses type_noisette_localiser()
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $noisette
 *        Tableau associatif descriptif de la noisette : les deux identifiants et le type de noisette.
 * @param string $type_noisette_actif
 *        Indique si le type de noisette est actif ou pas (au moins un plugin nécessité est désactivé). Prend
 *        les valeurs `oui` ou `non`.
 * @param string $type_noisette_necessite
 *        Tableau sérialisé des plugins necessités par le type de noisette. Sert uniquement à afficher l'avertissement
 *        éventuel sur les plugins inactifs.
 *
 * @return string
 *        Code HTML généré pour la noisette.
 */
function calculer_preview_noisette($plugin, $noisette, $type_noisette_actif, $type_noisette_necessite) {

	// Initialisation de la sortie.
	$preview = '';

	if ($type_noisette_actif == 'oui') {
		// On récupère le dossier de stockage des type de noisette afin de vérifier si un squelette de prévisualisation
		// existe pour le type de noisette concerné.
		include_spip('inc/utils');
		$squelette_preview = type_noisette_localiser($plugin, $noisette['type_noisette']) . '-preview';
		if (trouver_fond($squelette_preview)) {
			$preview = recuperer_fond($squelette_preview, $noisette);
		}
	} else {
		$plugins = unserialize($type_noisette_necessite);
		$plugins_inactifs = '';
		foreach ($plugins as $_plugin) {
			if (!defined('_DIR_PLUGIN_' . strtoupper($_plugin))) {
				$plugins_inactifs .= (!$plugins_inactifs ? '' : ', ') . $_plugin;
			}
		}
		$preview = recuperer_fond(
			'avertir_preview',
			array_merge($noisette, array('plugins_inactifs' => $plugins_inactifs))
		);
	}

	return $preview;
}
