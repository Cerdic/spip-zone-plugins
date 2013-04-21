<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


if (!defined('_LANGONET_PATTERN_FONCTION_L'))
	define("_LANGONET_PATTERN_FONCTION_L", "#\b_L\s*[(]\s*(['\"])([^\\1]+)\\1[^)]*#");
if (!defined('_LANGONET_FONCTION_L2'))
	define("_LANGONET_FONCTION_L2", "#\b_L *[(] *(')([^']+)'[^)]*#");

if (!defined('_LANGONET_PATTERN_FICHIERS_L'))
	define('_LANGONET_PATTERN_FICHIERS_L', '(?<!/charsets|/lang|/req)(/[^/]*\.(php))$');


/**
 * Verification de l'utilisation de la fonction _L() dans le code PHP.
 *
 * Cette fonction construit le tableau des occurrences du premier argument de _L.
 * Ce tableau est indexe par un representant canonique de chaque chaine trouvee.
 * Les valeurs de ce tableau sont des sous-tableaux indexes par le nom du fichier.
 * Chacun a pour valeur un sous-sous-tableau indexe par le numero de ligne, pointant
 * sur un sous-sous-sous-tableau des resultats des preg_match(donc encore des tableaux, indexe numeriquement)
 *
 * @param $module
 * 		nom du module de langue
 * @param $ou_fichier
 * 		racine de l'arborescence a verifier.
 * 		On n'examine pas les ultimes sous-repertoires charsets/,lang/ , req/ et /.
 * 		On n'examine que les fichiers php (voir le fichier regexp.txt).
 * @return array
 * 		Si une erreur se produit lors du deroulement de la fonction, le tableau resultat contient le libelle
 * 		de l'erreur dans l'index 'erreur'; sinon, cet index n'existe pas.
 */
function inc_langonet_verifier_l($module, $ou_fichier) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// Construire la liste des fichiers php dans lesquels rechercher la fonction _L()
	// On passe les arborescences une par une
	$fichiers = array();
	foreach($ou_fichier as $_arborescence) {
		$fichiers = array_merge(preg_files(_DIR_RACINE . $_arborescence, _LANGONET_PATTERN_FICHIERS_L));
	}

	// Chercher, pour chaque fichier collecté, le pattern de la fonction _L()
	if ($fichiers) {
		include_spip('inc/langonet_utils');

		$item_md5 = array();
		$fichier_non = array();
		foreach ($fichiers as $_fichier) {
			$contenu = file($_fichier);
			if ($contenu) {
				foreach ($contenu as $_no_ligne => $_ligne) {
					if (preg_match_all(_LANGONET_PATTERN_FONCTION_L, $_ligne, $m, PREG_SET_ORDER)) {
						foreach ($m as $_occurrence) {
							// Calcul du nom du raccourci de l'item de langue
							$index = langonet_index($_occurrence[2], $item_md5);
							// Stockage de ce raccourci
							$item_md5[$index] = $_occurrence[2];
							// Ajout de l'occurrence trouvée dans le fichier des erreurs
							$fichier_non[$index][$_fichier][$_no_ligne][] = $_occurrence;
						}
					}
				}
			}
		}

		$resultats['module'] = $module;
		$resultats['ou_fichier'] = $ou_fichier;
		$resultats['item_non'] = array_keys($item_md5);
		$resultats['fichier_non'] = $fichier_non;
		$resultats['item_md5'] = $item_md5;
	}
	else {
		$resultats['erreur'] = _T('langonet:message_nok_arborescence_l');
	}

	return $resultats;
}
?>
