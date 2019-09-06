<?php
/**
 * Génère, à partir d'un tableau de saisie le code javascript ajouté à la fin de #GENERER_SAISIES
 * pour produire un affichage conditionnel des saisies ayant une option afficher_si
 *
 * @param array  $saisies
 *                        Tableau de descriptions des saisies
 * @param string $id_form
 *                        Identifiant unique pour le formulaire
 *
 * @return text Code javascript
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

