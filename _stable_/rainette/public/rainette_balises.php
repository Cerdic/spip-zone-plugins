<?php
function balise_RAINETTE_INFOS($p) {
	$code_meteo = interprete_argument_balise(1,$p);
	$code_meteo = isset($code_meteo) ? str_replace('\'', '"', $code_meteo) : '""';
	$type_info = interprete_argument_balise(2,$p);
	$type_info = isset($type_info) ? str_replace('\'', '"', $type_info) : '""';

	$p->code = 'charger_infos('.$code_meteo.', '.$type_info.')';
	$p->interdire_scripts = false;
	return $p;
}

?>