<?php
// Les REGEXP de recherche de l'item de langue (voir le fichier regexp.txt)
// -- pour les fichiers .php et la detection de _L
define("_LANGONET_TROUVER_FONCTION_L_P", "`_L\([\"'](.+)(?:[,\"']|[\"'][,].*)\)`iUm");

/**
 * Vérification de l'utilisation de la fonction _L() dans le code PHP 
 *
 * @param string $ou_fichier
 * @return array
 */

// $ou_fichier   => racine de l'arborescence a verifier
function inc_langonet_verifier_l($ou_fichier) {

	// Initialisation du tableau des resultats
	// Si une erreur se produit lors du deroulement de la fonction, le tableau contient le libelle
	// de l'erreur dans $resultats['erreur'].
	// Sinon, cet index n'existe pas
	$resultats = array();

	// On cherche l'ensemble des items utilises dans l'arborescence $ou_fichier
	$utilises_brut = array('items' => array());
	// On ne scanne pas dans les ultimes sous-repertoires charsets/ ,
	// lang/ , req/ . On ne scanne que les fichiers php
	// (voir le fichier regexp.txt).
	foreach (preg_files(_DIR_RACINE.$ou_fichier, '(?<!/charsets|/lang|/req)(/[^/]*\.(php))$') as $_fichier) {
		foreach ($contenu = file($_fichier) as $ligne => $texte) {
			$trouver_item = _LANGONET_TROUVER_FONCTION_L_P;
			if (preg_match_all($trouver_item, $texte, $matches)) {
				$utilises_brut['items'] = array_merge($utilises_brut['items'], $matches[1]);
				// On collecte pour chaque item trouve les lignes et fichiers dans lesquels il est utilise
				foreach ($matches[1] as $_item_val) {
					$item_val = addcslashes($_item_val,"$()");
					preg_match("`.{0,8}_L\([\"']".$item_val.".{0,20}`is", $texte, $extrait);
					// On indexe avec le md5 de la valeur de _L() car parfois cette valeur
					// contient des caracteres non echappes qui perturbent l'indexation du tableau
					// Il faudra donc traiter l'affichage correspondant a cette option
					$item_tous[md5($_item_val)][$_fichier][$ligne][] = trim($extrait[0]);
				}
			}
		}
	}

	// On affine le tableau resultant en supprimant les doublons
	// et on construit la liste des items utilises mais non definis
	$item_non = array();
	$item_md5 = array();
	$fichier_non = array();
	foreach ($utilises_brut['items'] as $_cle => $_valeur) {
		if (!in_array($_valeur, $item_non)) {
			$item_non[] = $_valeur;
			$index = md5($_valeur);
			$item_md5[$index] = $_valeur;
			if (is_array($item_tous[$index])) {
				$fichier_non[$index] = $item_tous[$index];
			}
		}
	}

	// On prepare le tableau des resultats
	$resultats['ou_fichier'] = $ou_fichier;
	$resultats['item_non'] = $item_non;
	$resultats['fichier_non'] = $fichier_non;
	$resultats['item_md5'] = $item_md5;
	
	return $resultats;
}

?>