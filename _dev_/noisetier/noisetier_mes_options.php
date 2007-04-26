<?php
include_spip('base/noisetier');

function balise_INCLURE_NOISETTE($p) {
	$champ = phraser_arguments_inclure($p, true);
	$_contexte = argumenter_inclure($champ, $p->descr, $p->boucles, $p->id_boucle, false);

	if (isset($_contexte['fond'])) {
		// Critere d'inclusion {env} (et {self} pour compatibilite ascendante)
		if (isset($_contexte['env'])
		|| isset($_contexte['self'])
		) {
			$flag_env = true;
			unset($_contexte['env']);
		}
		$l = 'array(' . join(",\n\t", $_contexte) .')';
		if ($flag_env) {
			$l = "array_merge(\$Pile[0],$l)";
		}
		$p->code = "recuperer_fond('',".$l.",true, false)";
	} else {
		$n = interprete_argument_balise(1,$p);
		$p->code = '(($c = find_in_path(' . $n . ')) ? spip_file_get_contents($c) : "")';
	}


	$p->interdire_scripts = false; // la securite est assuree par recuperer_fond
	return $p;
}

?>