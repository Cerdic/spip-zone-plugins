<?php

/**
 * Gestion de l'affichage conditionnelle des saisies.
 * Partie spécifique js
 *
 * @package SPIP\Saisies\Afficher_si_js
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/saisies_afficher_si_commun');
include_spip('inc/saisies_lister');
/**
 * Transforme une condition afficher_si en condition js
 * @param string $condition
 * @param array $saisies_form les saisies du même formulaire. Nécessaire pour savoir quel type de test js on met.
 * @return string
**/
function saisies_afficher_si_js($condition, $saisies_form = array()) {
	if (!$condition) {
		return '';
	}
	$saisies_form = pipeline('saisies_afficher_si_js_saisies_form', $saisies_form);
	$saisies_form = saisies_lister_par_nom($saisies_form);
	if ($tests = saisies_parser_condition_afficher_si($condition)) {
		if (!saisies_afficher_si_verifier_syntaxe($condition, $tests)) {
			spip_log("Afficher_si incorrect. $condition syntaxe incorrecte", "saisies"._LOG_CRITIQUE);
			return '';
		}
		foreach ($tests as $test) {
			$expression = $test[0];
			$negation = isset($test['negation']) ? $test['negation'] : '' ;
			$champ = isset($test['champ']) ? $test['champ'] : '' ;
			$total = isset($test['total']) ? $test['total'] : '';
			$operateur = isset($test['operateur']) ? $test['operateur'] : '' ;
			$guillemet = isset($test['guillemet']) ? $test['guillemet'] : '' ;
			$negation = isset($test['negation']) ? $test['negation'] : '';
			$booleen = isset($test['booleen']) ? $test['booleen'] : '';
			$valeur = isset($test['valeur']) ? $test['valeur'] : '' ;
			$valeur_numerique = isset($test['valeur_numerique']) ? $test['valeur_numerique'] : '' ;
			$plugin = saisies_afficher_si_evaluer_plugin($champ, $negation);
			if ($plugin !== '') {
				$condition = str_replace($expression, $plugin ? 'true' : 'false', $condition);
			} elseif (stripos($champ, 'config:') !== false) {
				$config = saisies_afficher_si_get_valeur_config($champ);
				$test_modifie = eval('return '.saisies_tester_condition_afficher_si($config, $total, $operateur, $valeur, $negation).';') ? 'true' : 'false';
				$condition = str_replace($expression, $test_modifie, $condition);
			} elseif ($booleen)  {
				$condition = $condition;
			} else { // et maintenant, on rentre dans le vif du sujet : les champs. On délégue cela à une autre fonction
				$condition = str_replace($expression, saisies_afficher_si_js_champ($champ, $total, $operateur, $valeur, $valeur_numerique, $guillemet, $negation, $saisies_form), $condition);
			}
		}
	} else {
		if (!saisies_afficher_si_verifier_syntaxe($condition)) {
			spip_log("Afficher_si incorrect. $condition syntaxe incorrecte", "saisies"._LOG_CRITIQUE);
			return '';
		}
	}
	return str_replace('"', "&quot;", $condition);
}

/**
 * Génère à partir de l'analyse d'une condition afficher_si le test js, pour les champs
 * @param string $champ
 * @param string $total
 * @param string $operateur
 * @param string $valeur
 * @param string $valeur_numerique
 * @param string $valeur
 * @param string $guillemet
 * @param string $negation
 * @param string $saisies_form listée par nom
 * @return string condition_js
**/
function saisies_afficher_si_js_champ($champ, $total, $operateur, $valeur, $valeur_numerique, $guillemet, $negation, $saisies_form) {
	if (!isset($saisies_form[$champ])) {//La saisie conditionnante n'existe pas pour ce formulaire > on laisse tomber
		spip_log("Afficher_si incorrect. Champ $champ inexistant", "saisies"._LOG_CRITIQUE);
		return '';
	}
	$saisie = $saisies_form[$champ]['saisie'];

	// Cas d'une valeur numérique : pour le test js, cela ne change rien, on la passe comme valeur
	if (strlen($valeur_numerique) and !$valeur) {
		$valeur = $valeur_numerique;
	}

	// cas checkbox
	if ($saisie == 'checkbox') {
		return saisies_afficher_si_js_checkbox($champ, $total, $operateur, $valeur, $negation);
	}
	// cas fichier
	if ($saisie == 'fichiers') {
		$nb_fichiers = $saisies_form[$champ]['options']['nb_fichiers'];
		if (!$nb_fichiers) {
			$nb_fichiers = 1;
		} else {
			$nb_fichiers = intval($nb_fichiers);
		}
		return saisies_afficher_si_js_fichiers($champ, $total, $operateur, $valeur, $negation, $nb_fichiers);
	}
	// cas case
	if ($saisie == 'case') {// case
		return saisies_afficher_si_js_case($champ, $total, $operateur, $valeur, $guillemet, $negation);
	}
	// cas radio
	if ($saisie == 'radio' or $saisie == 'oui_non' or $saisie == 'true_false') {// radio et assimilés
		return saisies_afficher_si_js_radio($champ, $total, $operateur, $valeur, $guillemet, $negation);
	}
	// sinon cas par défaut
	return "$negation\$(form).find('[name=\"$champ\"]').val() $operateur $guillemet$valeur$guillemet";
}


/**
 * Génère les tests js pour les cas de case
 * @param string $champ
 * @param string $total
 * @param string $operateur
 * @param string $valeur
 * @param string $guillemet
 * @param string $negation
**/
function saisies_afficher_si_js_case($champ, $total, $operateur, $valeur, $guillemet, $negation) {
	if ($valeur  and $operateur) {
		return "$negation($(form).find(\".checkbox[name='$champ']\").is(':checked') ? $(form).find(\".checkbox[name='$champ']\").val() : '') $operateur $guillemet$valeur$guillemet";
	} else {
		return "$negation($(form).find(\".checkbox[name='$champ']\").is(':checked') ? $(form).find(\".checkbox[name='$champ']\").val() : '')";
	}
}

/**
 * Génère les tests js pour les cas de radio
 * @param string $champ
 * @param string $total
 * @param string $operateur
 * @param string $valeur
 * @param string $guillemet
 * @param string $negation
**/
function saisies_afficher_si_js_radio($champ, $total, $operateur, $valeur, $guillemet, $negation) {
	return "$negation$(form).find(\"[name='$champ']:checked\").val() $operateur $guillemet$valeur$guillemet";
}


/**
 * Génère les tests js pour les cas de fichiers
 * @param string $champ
 * @param string $total
 * @param string $operateur
 * @param string $valeur
 * @param string $negation
 * @param int $nb_fichiers
**/
function saisies_afficher_si_js_fichiers($champ, $total, $operateur, $valeur, $negation, $nb_fichiers) {
	$total = "$(form).find(\"[name^='cvtupload_fichiers_precedents[$champ]']\").length";
	$i = 0;
	while ($i < $nb_fichiers) {
		$total .= " + $(form).find(\"[name^='$champ']\")[$i].files.length";
		$i++;
	}
	$total = "($total)";
	$result = "$negation $total $operateur $valeur";
	return $result;
}

/**
 * Génère les tests js pour les cas de checkbox
 * @param string $champ
 * @param string $total
 * @param string $operateur
 * @param string $valeur
 * @param string $negation
**/
function saisies_afficher_si_js_checkbox($champ, $total, $operateur, $valeur, $negation) {
	if ($total) {// Cas 1 : on cherche juste à savoir le nombre total de case coché
		$result = "$negation$(form).find(\"[name^='$champ']:checked\").length $operateur $valeur";
		return $result;
	} else {// Cas 2 : on cherche à savoir s'il y a une case avec une certaine valeur
		// Convertir les conditions en test IN (compatibilité historique)
		if ($operateur == '==') {
			$operateur = 'IN';
		} elseif ($operateur == '!=') {
			$operateur = '!IN';
		}
		// La négation de l'opérateur remonte globalement
		if ($operateur == '!IN' and $negation) {
			$negation = '';
		} elseif ($operateur == '!IN') {
			$negation = '!';
		}
		// Spliter la valeur pour trouver toutes les cases qui doivent être cochées (ou pas)
		$valeurs = explode(',', $valeur);
		$valeurs = array_map('saisies_afficher_si_js_IN_individuel', $valeurs, array_fill(0,count($valeurs),$champ));
		$valeurs = implode(' || ', $valeurs);
		return "$negation($valeurs)";
	}
}

/**
 * Génère le sous-test js pour le cas où on a l'operateur IN ou !IN
 * c'est-à-dire, en pratique, pour les checkboxes
 * Par "sous-test js", nous entendons le test pour une valeur précise d'un checkbox
 * Attention : $valeur en premier (car fonction appelée dans un array_map())
 * @param string $valeur
 * @param string $champ
 * @return string
**/
function saisies_afficher_si_js_IN_individuel($valeur, $champ) {
	return "$(form).find('[name=\"$champ".'[]"'."][value=\"$valeur\"]').is(':checked')";
}
