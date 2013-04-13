<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Ces 2 REGEXP de recherche de _L
// doivent fournir les memes tableaux que les RegExp de recherche de <: :>
#       "`_L\([\"'](.+)(?:[,\"']|[\"'][,].*)\)`iUm"); # old
if (!defined('_LANGONET_FONCTION_L'))
	define("_LANGONET_FONCTION_L", '#\b_L *[(] *(")([^"]+)"[^)]*#');
if (!defined('_LANGONET_FONCTION_L2'))
	define("_LANGONET_FONCTION_L2", "#\b_L *[(] *(')([^']+)'[^)]*#");

if (!defined('_LANGONET_FILES'))
	define('_LANGONET_FILES', '(?<!/charsets|/lang|/req)(/[^/]*\.(php))$');


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

	$item_md5 = $fichier_non = array();
	foreach($ou_fichier as $rep){
		$files = array_merge(preg_files(_DIR_RACINE.$rep, _LANGONET_FILES));
	}
	foreach ($files as $_fichier) {
		foreach ($contenu = file($_fichier) as $ligne => $texte) {
			if (preg_match_all(_LANGONET_FONCTION_L, $texte, $m, PREG_SET_ORDER))
				foreach ($m as $occ) {
					$index = langonet_index($occ[2], $item_md5);
					$item_md5[$index] = $occ[2];
					$fichier_non[$index][$_fichier][$ligne][] = $occ;
				}
			elseif (preg_match_all(_LANGONET_FONCTION_L2, $texte, $m, PREG_SET_ORDER))
				foreach ($m as $occ) {
					$index = langonet_index($occ[2], $item_md5);
					$item_md5[$index] = $occ[2];
					$fichier_non[$index][$_fichier][$ligne][] = $occ;
				}
		}
	}
	return array(
		     'module' => $module,
		     'ou_fichier' => $ou_fichier,
		     'item_non' => array_keys($item_md5),
		     'fichier_non' => $fichier_non,
		     'item_md5' => $item_md5
		     );
}
?>
