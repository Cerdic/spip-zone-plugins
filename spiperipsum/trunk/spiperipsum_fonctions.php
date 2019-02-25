<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// -- balises du plugin utilisables dans les squelettes et modeles --

function balise_SPIPERIPSUM($p) {

	$langue = interprete_argument_balise(1, $p);
	$langue = isset($langue) ? str_replace('\'', '"', $langue) : '"en"';
	$jour = interprete_argument_balise(2, $p);
	$jour = isset($jour) ? str_replace('\'', '"', $jour) : '""';
	$lecture = interprete_argument_balise(3, $p);
	$lecture = isset($lecture) ? str_replace('\'', '"', $lecture) : '""';
	$info = interprete_argument_balise(4, $p);
	$info = isset($info) ? str_replace('\'', '"', $info) : '""';
	$service = interprete_argument_balise(5, $p);
	$service = isset($service) ? str_replace('\'', '"', $service) : '"evangelizo"';

	$p->code = 'spiperipsum_lire(' . $langue . ', ' . $jour . ', ' . $lecture . ', ' . $info . ', ' . $service . ')';
	$p->interdire_scripts = false;

	return $p;
}

// -- filtres du plugin utilisables dans les squelettes et modeles --

function spiperipsum_afficher($langue, $jour, $lecture, $mode, $service = 'evangelizo') {

	if (!$jour) $jour = _SPIPERIPSUM_JOUR_DEFAUT;
	if (!$lecture) $lecture = _SPIPERIPSUM_LECTURE_DEFAUT;
	if (!$mode) $mode = _SPIPERIPSUM_MODE_DEFAUT;
	if (!$service) $service = 'evangelizo';

	// Récupération des lectures pour le service demandé
	$charger = charger_fonction('spiperipsum_charger', 'inc');
	$tableau = $charger($langue, $jour, $service);

	$contexte = array();

	if (($lecture == _SPIPERIPSUM_LECTURE_DATE_TITRE)
	or ($lecture == _SPIPERIPSUM_LECTURE_DATE_ISO)
	or ($lecture == _SPIPERIPSUM_LECTURE_DATE_LITURGIQUE)) {
		if (isset($tableau['date'])) {
			$contexte = $tableau['date'];
		}
		$contexte = array_merge($contexte, array('lecture' => $lecture, 'mode' => $mode));
		$texte = recuperer_fond('modeles/date', $contexte);
	}
	else {
		if (isset($tableau[$lecture])) {
			$contexte = $tableau[$lecture];
		}
		$contexte = array_merge($contexte, array('lecture' => $lecture, 'mode' => $mode));

		if ($lecture == _SPIPERIPSUM_LECTURE_SAINT)
			$texte = recuperer_fond('modeles/saint', $contexte);
		elseif ($lecture == _SPIPERIPSUM_LECTURE_COMMENTAIRE)
			$texte = recuperer_fond('modeles/commentaire', $contexte);
		else
			$texte = recuperer_fond('modeles/lecture', $contexte);
	}

return $texte;
}

function spiperipsum_lire($langue, $jour, $lecture, $info, $service = 'evangelizo') {

	if (!$jour) $jour = _SPIPERIPSUM_JOUR_DEFAUT;
	if (!$lecture) $lecture = _SPIPERIPSUM_LECTURE_DEFAUT;
	if (!$info) $info = _SPIPERIPSUM_INFO_DEFAUT;
	if (!$service) $service = 'evangelizo';

	// Récupération des lectures pour le service demandé
	$charger = charger_fonction('spiperipsum_charger', 'inc');
	$tableau = $charger($langue, $jour, $service);

	// Pour la date, on peut utiliser au choix
	// - lecture = date et info = iso, liturgique et titre
	// - lecture = date_titre, date_iso et date_liturgique
	// Dans le deuxième cas il faut un traitement préalable pour rétablir la structure d'index standard
	if (substr($lecture, 0, 5) === 'date_') {
		$parties = explode('_', $lecture);
		$lecture = 'date';
		$info = $parties[1];
	}

	return $tableau[$lecture][$info];
}


/**
 * @param string $mode
 *
 * @return array|string
 */
function spiperipsum_lister_services($mode = 'tableau') {

	static $services = array();

	if (!isset($service[$mode])) {
		// On lit les fichiers php dans répertoire services/ du plugin sachant ce répertoire
		// contient exclusivement les api de chaque service dans un fichier unique appelé
		// alias_du_service.php
		$liste = array();
		if ($fichiers_api = glob(_DIR_PLUGIN_SPIPERIPSUM . '/services/*.php')) {
			foreach ($fichiers_api as $_fichier) {
				// On détermine l'alias du service
				$service = strtolower(basename($_fichier, '.php'));

				// On en déduit son nom.
				$liste[$service] = _T("spiperipsum:service_${service}_titre");
			}
		}

		// Par défaut la liste est fournie comme un tableau.
		// Si le mode demandé est 'liste' on renvoie une chaîne énumérée des alias de service séparée par des virgules.
		$services[$mode] = ($mode == 'tableau') ? $liste : implode(',', array_keys($liste));
	}

	return $services[$mode];
}


/**
 * @param string $mode
 *
 * @return array|string
 */
function spiperipsum_lister_lectures($mode = 'tableau') {

	static $lectures = array();

	if (!isset($lectures[$mode])) {
		$liste = array();
		foreach (explode(':', _SPIPERIPSUM_LECTURE_LISTE) as $_lecture) {
			$liste[$_lecture] = _T("spiperipsum:lecture_${_lecture}_titre");
		}

		// Par défaut la liste est fournie comme un tableau.
		// Si le mode demandé est 'liste' on renvoie une chaîne énumérée des alias de service séparée par des virgules.
		$lectures[$mode] = ($mode == 'tableau') ? $liste : implode(',', array_keys($liste));
	}

	return $lectures[$mode];
}
