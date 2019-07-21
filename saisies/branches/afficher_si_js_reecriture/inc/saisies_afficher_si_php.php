<?php

/**
 * Gestion de l'affichage conditionnelle des saisies.
 * Partie spécifique php
 *
 * @package SPIP\Saisies\Afficher_si_php
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/saisies_afficher_si_commun');

/**
 * Lorsque l'on affiche les saisies (#VOIR_SAISIES), les saisies ayant une option afficher_si
 * et dont les conditions ne sont pas remplies doivent être retirées du tableau de saisies.
 *
 * Cette fonction sert aussi lors de la vérification des saisies avec saisies_verifier().
 * À ce moment là, les saisies non affichées sont retirées de _request
 * (on passe leur valeur à NULL).
 *
 * @param array      $saisies
 *                            Tableau de descriptions de saisies
 * @param array|null $env
 *                            Tableau d'environnement transmis dans inclure/voir_saisies.html,
 *                            NULL si on doit rechercher dans _request (pour saisies_verifier()).
 *
 * @return array
 *               Tableau de descriptions de saisies
 */
function saisies_verifier_afficher_si($saisies, $env = null) {
	// eviter une erreur par maladresse d'appel :)
	if (!is_array($saisies)) {
		return array();
	}
	// Economiser un peu de calcul, notamment pour formidable
	static $precedent_saisies = array();
	static $precedent_env = array();
	if ($precedent_saisies == $saisies and $precedent_env == $env) {
		return $saisies;
	}
	$precedent_saisies = $saisies;
	$precedent_env = $env;
	foreach ($saisies as $cle => $saisie) {
		if (isset($saisie['options']['afficher_si'])) {
			$condition = $saisie['options']['afficher_si'];
			// Est-ce uniquement au remplissage?
			if (isset($saisie['options']['afficher_si_remplissage_uniquement'])
				and $saisie['options']['afficher_si_remplissage_uniquement']=='on'){
				$remplissage_uniquement = true;
			} else {
				$remplissage_uniquement = false;
			}

			// On transforme en une condition PHP valide
			$ok = saisies_evaluer_afficher_si($condition, $env);
			if (!$ok) {
				if ($remplissage_uniquement == false or is_null($env)) {
					unset($saisies[$cle]);
				}
				if (is_null($env)) {
					if ($saisie['saisie'] == 'explication') {
						unset($saisies[$cle]);
					} else {
						saisies_set_request_null_recursivement($saisie);
					}
				}
			}
		}
		if (isset($saisies[$cle]['saisies'])) {
			// S'il s'agit d'un fieldset ou equivalent, verifier les sous-saisies
			$saisies[$cle]['saisies'] = saisies_verifier_afficher_si($saisies[$cle]['saisies'], $env);
		}
	}
	return $saisies;
}



/**
 * Pose un set_request null sur une saisie et toute ses sous-saisies.
 * Utiliser notamment pour annuler toutes les sous saisies d'un fieldeset
 * si le fieldset est masquée à cause d'un afficher_si.
 * @param array $saisie
**/
function saisies_set_request_null_recursivement($saisie) {
	set_request($saisie['options']['nom'], null);
	if (isset($saisie['saisies'])) {
		foreach ($saisie['saisies'] as $sous_saisie) {
			saisies_set_request_null_recursivement($sous_saisie);
		}
	}
}

/**
 * Récupère la valeur d'un champ à tester avec afficher_si
 * Si le champ est de type @config:xx@, alors prend la valeur de la config
 * sinon en _request() ou en $env["valeurs"]
 * @param string $champ: le champ
 * @param null|array $env
 * @return  la valeur du champ ou de la config
 **/
function saisies_afficher_si_get_valeur_champ($champ, $env) {
	$plugin = saisies_afficher_si_evaluer_plugin($champ);
	$config = saisies_afficher_si_get_valeur_config($champ);
	if ($plugin !== '') {
		$champ = $plugin;
	} elseif ($config) {
		$champ = $config;
	} elseif (is_null($env)) {
		$champ = _request($champ);
	} else {
		$champ = $env["valeurs"][$champ];
	}
	return $champ;
}


/**
 * Prend un test conditionnel,
 * le sépare en une série de sous-tests de type champ - operateur - valeur
 * remplace chacun de ces sous-tests par son résultat
 * renvoie la chaine transformé
 * @param string $condition
 * @param array|null $env
 *   Tableau d'environnement transmis dans inclure/voir_saisies.html,
 *   NULL si on doit rechercher dans _request (pour saisies_verifier()).
 * @return string $condition
**/
function saisies_transformer_condition_afficher_si($condition, $env = null) {
	if ($tests = saisies_parser_condition_afficher_si($condition)) {
		if (!saisies_afficher_si_secure($condition, $tests)) {
			spip_log("Afficher_si incorrect. $condition non sécurisée", "saisies"._LOG_CRITIQUE);
			return '';
		}
		foreach ($tests as $test) {
			$expression = $test[0];
			$champ = saisies_afficher_si_get_valeur_champ($test['champ'], $env);
			$operateur = isset($test['operateur']) ? $test['operateur'] : null;
			$negation = isset($test['negation']) ? $test['negation'] : '';
			if (isset($test['valeur_numerique'])) {
				$valeur = intval($test['valeur_numerique']);
			} elseif (isset($test['valeur'])) {
				$valeur = $test['valeur'];
			} else {
				$valeur = null;
			}

			$test_modifie = saisies_tester_condition_afficher_si($champ, $operateur, $valeur, $negation) ? 'true' : 'false';
			$condition = str_replace($expression, $test_modifie, $condition);
		}
	} else {
		$condition = str_replace(' ', '', $condition);
		$condition_possible = array("!false", "false", "true", "!true");
		if (!in_array($condition, $condition_possible)){
			spip_log("Afficher_si incorrect : $condition", "saisies"._LOG_CRITIQUE);
			$condition = true;
		}
	}
	return $condition;
}

/**
 * Evalue un afficher_si
 * @param string $condition (déjà checkée en terme de sécurité)
 * @param array|null $env
 *   Tableau d'environnement transmis dans inclure/voir_saisies.html,
 *   NULL si on doit rechercher dans _request (pour saisies_verifier()).
 * @return bool le résultat du test
**/
function saisies_evaluer_afficher_si($condition, $env = null) {
	$condition = saisies_transformer_condition_afficher_si($condition, $env);
	if ($condition) {
		eval('$ok = '.$condition.';');
	} else {
		$ok = true;
	}
	return $ok;
}
