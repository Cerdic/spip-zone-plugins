<?php

// conversion de couleur
function chartjs_hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }

	   $rgb = array($r, $g, $b);
	   return implode(",", $rgb); // returns the rgb values separated by commas
}

// Convertir des mots spéparés par des virgules en tableau (pour utiliser dans un array_map)
function chartjs_explode_virgule($texte){
	$tableau = explode(',', $texte);
	return $tableau;
}

// Typer les valeurs texte d'un tableau
function chartjs_array_typecaster($table){
	$table = chartjs_array_map_recursive('chartjs_typecaster', $table);
	return $table;
}

// Typer une valeur texte en entier ou booléen
function chartjs_typecaster($v){
	if (is_numeric($v)) {
		$v = floatval($v);
	} elseif ($v == 'true') {
		$v = true;
	} elseif ($v == 'false') {
		$v = false;
	}
	return $v;
}

// Pas dans php :(
function chartjs_array_map_recursive($function, $table) {
	$out = array();
	if (is_array($table)) {
		foreach ($table as $k => $v) {
			$out[$k] = ($v and is_array($v)) ? chartjs_array_map_recursive($function, $v) : $function($v);
		}
	}
	return $out;
}

// Pas dans php :(
function chartjs_array_filter_recursive($input) {
	foreach ($input as &$value) {
		if (is_array($value)) {
			$value = chartjs_array_filter_recursive($value);
		}
	}
	return array_filter($input);
}