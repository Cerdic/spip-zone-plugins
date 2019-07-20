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

/**
 * Transforme une condition afficher_si en condition js
 * @param string $condition
 * @return string
**/
function saisies_afficher_si_js($condition) {
	if ($tests = saisies_parser_condition_afficher_si($condition)) {
		foreach ($tests as $test) {
			$expression = $test[0];
			$negation = isset($test['negation']) ? $test['negation'] : '' ;
			$champ = isset($test['champ']) ? $test['champ'] : '' ;
			$operateur = isset($test['operateur']) ? $test['operateur'] : '' ;
			$guillemet = isset($test['guillemet']) ? $test['guillemet'] : '' ;
			$negation = isset($test['negation']) ? $test['negation'] : '';
			$valeur = isset($test['valeur']) ? $test['valeur'] : '' ;
			$valeur_numerique = isset($test['valeur_numerique']) ? $test['valeur_numerique'] : '' ;
			$plugin = saisies_afficher_si_evaluer_plugin($champ, $negation);
			if ($plugin !== '') {
				$condition = str_replace($expression, $plugin ? 'true' : 'false', $condition);
			} elseif (stripos($champ, 'config') !== false) {
				$config = saisies_afficher_si_get_valeur_config($champ);
				$test_modifie = eval('return '.saisies_tester_condition_afficher_si($config, $operateur, $valeur, $negation).';') ? 'true' : 'false';
				$condition = str_replace($expression, $test_modifie, $condition);
			} else { // et maintenant, on rentre dans le vif du sujet : les champs. On délégue cela à une autre fonction
				$condition = str_replace($expression, saisies_afficher_si_js_champ($champ, $operateur, $valeur, $valeur_numerique, $guillemet, $negation), $condition);
			}
		}
	}
	return $condition;
}

/**
 * Génère à partir de l'analyse d'une condition afficher_si le test js, pour les champs
 * @param string $champ
 * @param string $operateur
 * @param string $valeur
 * @param string $valeur_numerique
 * @param string $valeur
 * @param string $guillemet
 * @param string $negation
 * @return string condition_js
**/
function saisies_afficher_si_js_champ($champ, $operateur, $valeur, $valeur_numerique, $guillemet, $negation) {
	// Cas d'une valeur numérique : pour le test js, cela ne change rien, on la passe comme valeur
	if ($valeur_numerique and !$valeur) {
		$valeur = $valeur_numerique;
	}
	// Guillemets : si double, les échapper
	if ($guillemet == '"') {
		$guillemet = '\"';
	}
	// Cas de chekbox => convertir les conditions en test IN (compatibilité historique)
	if (stripos($champ, 'checkbox') !== false) {
		if ($operateur == '==') {
			$operateur = 'IN';
		} elseif ($operateur == '!=') {
			$operateur = '!IN';
		}
	}
	if ($operateur) {
		if ($operateur != 'IN' and $operateur != '!IN') {
			return "$negation\$form().find('[name=$champ]').val() $operateur $guillemet$valeur$guillemet";
		} else {
			// cas des checkbox (au sens saisie @checkbox_xx@) => operateur IN ou !IN
			return saisies_afficher_si_js_IN($champ, $operateur, $valeur, $negation);
		}
	}
}


/**
 * Génère les tests js pour le cas où on a l'operateur IN ou !IN
 * c'est-à-dire, en pratique, pour les checkboxes
 * @param string $champ
 * @param string $operateur
 * @param string $valeur
 * @param string $negation
**/
function saisies_afficher_si_js_IN($champ, $operateur, $valeur, $negation) {
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
	return "$(form).find(checkbox[name=$champ".'[]'."][value='$valeur']).is(':checked')";
}
/**
 * Génère, à partir d'un tableau de saisie le code javascript ajouté à la fin de #GENERER_SAISIES
 * pour produire un affichage conditionnel des saisies ayant une option afficher_si
 *
 * @param array  $saisies
 *                        Tableau de descriptions des saisies
 * @param string $id_form
 *                        Identifiant unique pour le formulaire
 *
 * @return text
 *              Code javascript
 */
function saisies_generer_js_afficher_si($saisies, $id_form) {
	$i = 0;
	$saisies = saisies_lister_par_nom($saisies, true);
	$code = '';
	$code .= "$(function(){\n\tchargement=true;\n";
	$code .= "\tverifier_saisies_".$id_form." = function(form){\n";

	if (!defined('_SAISIES_AFFICHER_SI_JS_SHOW')) {
		define ('_SAISIES_AFFICHER_SI_JS_SHOW', 'show(400)');
	}
	if (!defined('_SAISIES_AFFICHER_SI_JS_HIDE')) {
		define ('_SAISIES_AFFICHER_SI_JS_HIDE', 'hide(400)');
	}
	foreach ($saisies as $saisie) {
		// on utilise comme selecteur l'identifiant de saisie en priorite s'il est connu
		// parce que conteneur_class = 'tableau[nom][option]' ne fonctionne evidement pas
		// lorsque le name est un tableau
		if (isset($saisie['options']['afficher_si']) && trim($saisie['options']['afficher_si'])) {
			++$i;
			// Les [] dans le nom de la saisie sont transformés en _ dans le
			// nom de la classe, il faut faire pareil
			$nom_underscore = rtrim(
					preg_replace('/[][]\[?/', '_', $saisie['options']['nom']),
					'_'
			);
			// retrouver la classe css probable
			switch ($saisie['saisie']) {
				case 'fieldset':
					$class_li = 'fieldset_'.$nom_underscore;
					break;
				case 'explication':
					$class_li = 'explication_'.$nom_underscore;
					break;
				default:
					// Les [] dans le nom de la saisie sont transformés en _ dans le
					// nom de la classe, il faut faire pareil
					$class_li = 'editer_'.$nom_underscore;
			}
			$condition = trim($saisie['options']['afficher_si']);
			// retrouver l'identifiant
			$identifiant = '';
			if (isset($saisie['identifiant']) and $saisie['identifiant']) {
				$identifiant = $saisie['identifiant'];
			}
			// On transforme en une condition valide
			preg_match_all('#@(.+)@#U', $condition, $matches);
			foreach ($matches[1] as $nom) {
				switch ($saisies[$nom]['saisie']) {
					case 'radio':
					case 'oui_non':
					case 'true_false':
						$condition = preg_replace('#@'.preg_quote($nom).'@#U', '$(form).find("[name=\''.$nom.'\']:checked").val()', $condition);
						break;
					case 'case':
						$condition = preg_replace('#@'.preg_quote($nom).'@#U', '($(form).find(".checkbox[name=\''.$nom.'\']").is(":checked") ? $(form).find(".checkbox[name=\''.$nom.'\']").val() : "")', $condition);
						break;
						}
						break;
				}
			}
			if ($identifiant) {
				$sel = "[data-id='$identifiant']";
			} else {
				$sel = ".$class_li";
			}
			$code .= "\tif (".$condition.") {\n"
							 .	"\t\t$(form).find(\"$sel\")."._SAISIES_AFFICHER_SI_JS_SHOW.".addClass('afficher_si_visible').removeClass('afficher_si_masque');\n";
			if (html5_permis()) {
			$code .=	"\t\t$(form).find(\"$sel [data-afficher-si-required]\").attr(\"required\",true).attr(\"data-afficher-si-required\",null);\n";
			}
			$code .=	"\t}\n";
			$code .= "\telse {\n";
			if (html5_permis()) {
				$code .= "\t\t$(form).find(\"$sel [required]\").attr(\"required\",false).attr(\"data-afficher-si-required\",true);\n";
			}
			$code .= "\t\tif (chargement==true) {\n"
					."\t\t\t$(form).find(\"$sel\")."._SAISIES_AFFICHER_SI_JS_HIDE.".addClass('afficher_si_masque').removeClass('afficher_si_visible').css".'("display","none")'.";\n"
					."\t\t}\n"
					."\t\telse {\n"
					."\t\t\t$(form).find(\"$sel\")."._SAISIES_AFFICHER_SI_JS_HIDE.".addClass('afficher_si_masque').removeClass('afficher_si_visible');\n"
					."\t\t};\n"
					."\t}\n";
		}
	}
	$code .= "$(form).trigger('saisies_afficher_si_js_ok');\n";
	$code .= "};\n";
	$code .= "\t".'$("#afficher_si_'.$id_form.'").parents("form").each(function(){'."\n\t\t".'verifier_saisies_'.$id_form.'(this);});'."\n";
	$code .= "\t".'$("#afficher_si_'.$id_form.'").parents("form").change(function(){'."\n\t\t".'verifier_saisies_'.$id_form.'(this);});'."\n";
	$code .= "\tchargement=false;})\n";

	if (!defined('_SAISIES_AFFICHER_SI_JS_LISIBLE')) {
		define('_SAISIES_AFFICHER_SI_JS_LISIBLE', false);
	}
	if (!_SAISIES_AFFICHER_SI_JS_LISIBLE) {
		// il suffit de régler cette constante à TRUE pour afficher le js de manière plus lisible (et moins sibyllin)
		$code = str_replace("\n", '', $code); //concatener
		$code = str_replace("\t", '', $code); //concatener
	}
	return $i > 0 ? $code : '';
}

