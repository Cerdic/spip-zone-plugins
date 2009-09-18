<?php
/**
 * Plugin Inscription2 pour SPIP
 * Licence GPL v3
 *
 * Fichier de retrocompatibilité pour php4
 */

/**
 * Calcul de l'intersection de deux tableaux
 * Emule la fonction pour php <5.1
 *
 * @author Rod Byrnes
 * @see http://fr.php.net/manual/fr/function.array-intersect-key.php#74956
 */

if (!function_exists('array_intersect_key')) {
	function array_intersect_key($isec, $keys) {
		$argc = func_num_args();
		if ($argc > 2){
			for ($i = 1; !empty($isec) && $i < $argc; $i++){
				$arr = func_get_arg($i);
				foreach (array_keys($isec) as $key){
					if (!isset($arr[$key])){
						unset($isec[$key]);
					}
				}
			}
			return $isec;
		}
		else{
			$res = array();
			foreach (array_keys($isec) as $key){
				if (isset($keys[$key])){
					$res[$key] = $isec[$key];
				}
			}
			return $res;
		}
	}
}

/**
 * Création d'un tableau à partir de deux autres tableaux
 * Requis pour array_fill_keys
 *
 * @author Zoran
 * @see http://fr2.php.net/manual/fr/function.array-combine.php#82244
 */

if (!function_exists('array_combine')) {
	function array_combine($arr1, $arr2) {
		$out = array();

		$arr1 = array_values($arr1);
		$arr2 = array_values($arr2);

		foreach($arr1 as $key1 => $value1) {
			$out[(string)$value1] = $arr2[$key1];
		}

		return $out;
	}
}

/**
 * Remplissage d'un tableau avec des valeurs, en spécifiant les clés
 *
 * @author matrebatre
 * @see http://fr2.php.net/manual/fr/function.array-fill-keys.php#83962
 */

if (!function_exists('array_fill_keys')) {
	function array_fill_keys($keys ,$value){
		return array_combine($keys,array_fill(0,count($keys),$value));
	}
}
?>
