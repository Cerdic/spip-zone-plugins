<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Verification de l'utilisation de la fonction _L() dans le code PHP 
 *
 */
// Les REGEXP de recherche de l'item de langue (voir le fichier regexp.txt)
// -- pour les fichiers .php et la detection de _L
define("_LANGONET_FONCTION_L", 
#       "`_L\([\"'](.+)(?:[,\"']|[\"'][,].*)\)`iUm"); # old
	'#\b_L *[(] *"([^"]*)"[^)]*#');

define("_LANGONET_FONCTION_L2", 
	"#\b_L *[(] *'([^']*)'[^)]*#");

// Si une erreur se produit lors du deroulement de la fonction,
// le tableau resultat contient le libelle
// de l'erreur dans $resultats['erreur'];
// sinon, cet index n'existe pas.

// $ou_fichier   => racine de l'arborescence a verifier
// On n'examine pas les ultimes sous-repertoires charsets/,lang/ , req/ et /.
// On n'examine que les fichiers php
// (voir le fichier regexp.txt).

define('_LANGONET_FILES', '(?<!/charsets|/lang|/req)(/[^/]*\.(php))$');

// Construit le tableau des occurrences du premier argument de _L.
// Ce tableau est indexe par un representant canonique de chaque chaine trouvee
// Les valeurs de ce tableau sont des sous-tableaux indexes par le nom du fichier
// Chacun a pour valeur un sous-sous-tableau indexe par le numero de ligne,
// pointant sur un sous-sous-sous-tableau des appels complets de _L

// @param string $module
// @param string $ou_fichier
// @return array

function inc_langonet_verifier_l($module, $ou_fichier) {

	$item_md5 = array();
	$fichier_non = array();
	$files = preg_files(_DIR_RACINE.$ou_fichier, _LANGONET_FILES);
	foreach ($files as $_fichier) {
		foreach ($contenu = file($_fichier) as $ligne => $texte) {
			if (preg_match_all(_LANGONET_FONCTION_L, $texte, $m, PREG_SET_ORDER))
				foreach ($m as $occ) {
					$index = langonet_index_l($occ[1], $item_md5);
					$item_md5[$index] = $occ[1];
					$fichier_non[$index][$_fichier][$ligne][] = trim($occ[0]);
				}
			if (preg_match_all(_LANGONET_FONCTION_L2, $texte, $m, PREG_SET_ORDER))
				foreach ($m as $occ) {
					$index = langonet_index_l($occ[1], $item_md5);
					$item_md5[$index] = $occ[1];
					$fichier_non[$index][$_fichier][$ligne][] = trim($occ[0]);
				}
		}
	}

	$resultats['module'] = $module;
	$resultats['ou_fichier'] = $ou_fichier;
	$resultats['item_non'] = array_keys($item_md5);
	$resultats['fichier_non'] = $fichier_non;
	$resultats['item_md5'] = $item_md5;
	return $resultats;
}

// Calcul du representant canonique d'un premier argument de _L.
// C'est un transcodage ASCII, reduits aux 32 premiers caracteres,
// les caracteres non alphabetiques etant remplaces par un souligne.
// On elimine les repetitions de mots pour evacuer le cas frequent truc: @truc@
// Si plus que 32 caracteres, on elimine les mots de moins de 3 lettres.
// Si toujours trop, on coupe au dernier mot complet avant 32 caracteres.
// C'est donc le tableau des chaines de langues manquant;
// toutefois, en cas d'homonymie d'index, on prend le md5, qui est illisible.

// @param string $occ
// @param array item_md5
// @return string

function langonet_index_l($occ, $item_md5)
{
	$index = textebrut($occ);
	$index = preg_replace('/\\\\[nt]/', ' ', $index);
	$index = strtolower(translitteration($index));
	$index = trim(preg_replace('/\W+/', ' ', $index));
	$index = preg_replace('/\b(\w+)\W+\1/', '\1', $index);
	if (strlen($index) > 32) {
	  // trop long: abandonner les petits mots
		$index = preg_replace('/\b\w{1,3}\W/', '', $index);
		if (strlen($index) > 32) {
			// tant pis mais couper proprement
			$index = substr($index, 0, 32);
			$index = substr($index, 0, strrpos($index,' '));
		}
	}
	$index = str_replace(' ', '_', trim($index));
	if (isset($item_md5[$index]) AND $item_md5[$index] !== $occ)
		$index = md5($occ);

	return $index;
}
?>
