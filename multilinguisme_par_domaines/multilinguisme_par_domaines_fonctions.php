<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function multilinguisme_par_domaines_trouver_url_lang($lang) {
	include_spip('inc/config');
	$url_base = url_de_base();
	if ($t_domaines = lire_config('multilinguisme_par_domaines/domaines_'.$lang)) {
		$domaines = explode("\n", $t_domaines);
		if (strlen($domaines[0]) > 1) {
			$http = (
				(isset($_SERVER["SCRIPT_URI"]) and
					substr($_SERVER["SCRIPT_URI"], 0, 5) == 'https')
				or (isset($_SERVER['HTTPS']) and
					test_valeur_serveur($_SERVER['HTTPS']))
			) ? 'https' : 'http';
			$host = $http.'://'.$domaines[0].'/';
			return $host;
		}
	}
	return url_de_base();
}

// Surcharges

function balise_URL_($p) {
	include_spip('balise/url_');
	$nom = $p->nom_champ;
	if ($nom === 'URL_') {
		$msg = array('zbug_balise_sans_argument', array('balise' => ' URL_'));
		erreur_squelette($msg, $p);
		$p->interdire_scripts = false;
		return $p;
	} elseif ($f = charger_fonction($nom, 'balise', true)) {
		return $f($p);
	} else {
		$nom = strtolower($nom);
		$code = generer_generer_url(substr($nom, 4), $p);
		$code = champ_sql($nom, $p, $code);
		$p->code = $code;
		if (!$p->etoile) {
			$p->code = "vider_url($code)";
		}
		if (substr($nom, 4) == "rubrique" || substr($nom, 4) == "article")
			$p->code = '(($GLOBALS[\'lang\'] != $Pile[$P][\'lang\']) ? multilinguisme_par_domaines_trouver_url_lang($Pile[$SP][\'lang\']).'.$p->code.' : '.$p->code.')';
		$p->interdire_scripts = false;

		return $p;
	}
}