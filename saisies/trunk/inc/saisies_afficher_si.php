<?php

/**
 * Gestion de l'affichage conditionnelle des saisies
 *
 * @package SPIP\Saisies\Afficher_si
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
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
	foreach ($saisies as $saisie) {
		// on utilise comme selecteur l'identifiant de saisie en priorite s'il est connu
		// parce que conteneur_class = 'tableau[nom][option]' ne fonctionne evidement pas
		// lorsque le name est un tableau
		if (isset($saisie['options']['afficher_si'])) {
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
			$condition = isset($saisie['options']['afficher_si']) ? $saisie['options']['afficher_si'] : '';
			// retrouver l'identifiant
			$identifiant = '';
			if (isset($saisie['identifiant']) and $saisie['identifiant']) {
				$identifiant = $saisie['identifiant'];
			}
			// On gère le cas @plugin:non_plugin@
			preg_match_all('#@plugin:(.+)@#U', $condition, $matches);
			foreach ($matches[1] as $plug) {
				if (defined('_DIR_PLUGIN_'.strtoupper($plug))) {
					$condition = preg_replace('#@plugin:'.$plug.'@#U', 'true', $condition);
				} else {
					$condition = preg_replace('#@plugin:'.$plug.'@#U', 'false', $condition);
				}
			}
			// On gère le cas @config:plugin:meta@ suivi d'un test
			preg_match_all('#@config:(.+):(.+)@#U', $condition, $matches);
			foreach ($matches[1] as $plugin) {
				$config = lire_config($plugin);
				$condition = preg_replace('#@config:'.$plugin.':'.$matches[2][0].'@#U', '"'.$config[$matches[2][0]].'"', $condition);
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
					case 'checkbox':
						/**
						 * Faire fonctionner @checkbox_xx@ == 'valeur' et @checkbox_xx@ != 'valeur'
						 */
						$condition = preg_replace('#@(.+)@\s*(==|(!)=)\s*(\'[^\']*\'|"[^"]*")#U', "@$1@ $3IN $4", $condition );
						/**
						 * Faire fonctionner @checkbox_xx@ IN 'valeur' ou @checkbox_xx@ !IN 'valeur'
						 */
						preg_match_all('#@(.+)@\s*(!IN|IN)\s*[\'"](.*)[\'"]#U', $condition, $matches3);
						foreach ($matches3[3] as $key => $value) {
							$not = '';
							if ($matches3[2][$key] == '!IN') {
								$not = '!';
							}
							$values = explode(',', $value);
							$new_condition = $not.'(';
							foreach ($values as $key2 => $cond) {
								if ($key2 > 0) {
									$new_condition .= ' || ';
								}
								$new_condition .= '($(form).find(".checkbox[name=\''.$nom.'[]\'][value='.$cond.']").is(":checked") ? $(form).find(".checkbox[name=\''.$nom.'[]\'][value='.$cond.']").val() : "") == "'.$cond.'"';
							}
							$new_condition .= ')';
							$condition = str_replace($matches3[0][$key], $new_condition, $condition);
						}
						break;
					default:
						$condition = preg_replace('#@'.preg_quote($nom).'@#U', '$(form).find("[name=\''.$nom.'\']").val()', $condition);
				}
			}
			if ($identifiant) {
				$sel = "[data-id='$identifiant']";
			} else {
				$sel = ".$class_li";
			}
			$code .= "\tif (".$condition.") {\n"
							 .	"\t\t$(form).find(\"$sel\").show(400);\n";
			if (html5_permis()) {
			$pour_html_5 = 	"$sel.obligatoire > input, "// si le afficher_si porte directement sur le input
							."$sel .obligatoire > input, "// si le afficher_si porte sur le fieldset
							."$sel.obligatoire > textarea, "// si le afficher_si porte directement sur le textearea
							."$sel .obligatoire > textarea, "// si le afficher_si porte sur le fiedset
							."$sel.obligatoire > select, "//si le afficher_si porte directement sur le select
							."$sel .obligatoire > select";//si le afficher_si porte sur le fieldset
			$code .=	"\t\t$(form).find("
							.'"'."$pour_html_5\")".
							".attr(\"required\",true);\n";
			}
			$code .=	"\t}\n";
			$code .= "\telse {\n";
			if (html5_permis()) {
			 	$code .= "\t\t$(form).find(\n\t\t\t"
			 				.'"'."$pour_html_5\")\n"
			 				."\t\t.attr(".'"required"'.",false);\n";
			}
			$code .= "\t\tif (chargement==true) {\n"
					."\t\t\t$(form).find(\"$sel\").hide(400).css".'("display","none")'.";\n"
					."\t\t}\n"
					."\t\telse {\n"
					."\t\t\t$(form).find(\"$sel\").hide(400);\n"
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
	foreach ($saisies as $cle => $saisie) {
		if (isset($saisie['options']['afficher_si'])) {
			$condition = $saisie['options']['afficher_si'];
			// Si tentative de code malicieux, on rejete
			if (!saisies_verifier_securite_afficher_si($condition)) {
				spip_log("Afficher_si malicieuse : $condition", "saisies"._LOG_CRITIQUE);
				$condition = '$ok';
			}
			// Est-ce uniquement au remplissage?
			if (isset($saisie['options']['afficher_si_remplissage_uniquement'])
				and $saisie['options']['afficher_si_remplissage_uniquement']=='on'){
				$remplissage_uniquement = true;
			} else {
				$remplissage_uniquement = false;
			}

			// On gère le cas @plugin:non_plugin@
			preg_match_all('#@plugin:(.+)@#U', $condition, $matches);
			foreach ($matches[1] as $plug) {
				if (defined('_DIR_PLUGIN_'.strtoupper($plug))) {
					$condition = preg_replace('#@plugin:'.$plug.'@#U', 'true', $condition);
				} else {
					$condition = preg_replace('#@plugin:'.$plug.'@#U', 'false', $condition);
				}
			}
			// On gère le cas @config:plugin:meta@ suivi d'un test
			preg_match_all('#@config:(.+):(.+)@#U', $condition, $matches);
			foreach ($matches[1] as $plugin) {
				$config = lire_config($plugin);
				$condition = preg_replace('#@config:'.$plugin.':'.$matches[2][0].'@#U', '"'.$config[$matches[2][0]].'"', $condition);
			}
			// On transforme en une condition PHP valide
			$condition_originale = $condition;
			if (is_null($env)) {
				$condition = preg_replace('#@(.+)@#U', '_request(\'$1\')', $condition);
			} else {
				$condition = preg_replace('#@(.+)@#U', '$env["valeurs"][\'$1\']', $condition);
			}
			/**
			 * Tester si la condition utilise des champs qui sont des tableaux
			 * Si _request() ou $env["valeurs"] est un tableau, changer == et != par in_array et !in_array
			 * TODO: c'est vraiment pas terrible comme fonctionnement
			 */
			preg_match_all('/(_request\([\'"].*?[\'"]\)|\$env\[[\'"].*?[\'"]\]\[[\'"].*?[\'"]\])\s*(!=|==|IN|!IN)\s*[\'"](.*?)[\'"]/', $condition, $matches);
			foreach ($matches[1] as $key => $val) {
				eval('$requete = '.$val.';');
				//Pour eviter une fatale erreur si on évalue une chose qui devrait normalement être un tableau mais qui n'a pas été envoyé (type checkbox), si la chose en question est null, la transformer en tableau vide. Pareil c'est pas terrible.
				if (is_null($requete)) {
					$requete = array();
					//C'est un request, alors on va faire un set_request
					if (strpos($val, '_request') === 0) {
						$set_tableau = "set$val";
						$set_tableau = str_replace(")",",array())",$set_tableau);
					} elseif (strpos($val, '$env') === 0) {//C'est un tablau direct
						$set_tableau = ("$val = array()");
					}
					if (isset($set_tableau)) {
						eval("$set_tableau;");
					}
				}
				if (is_array($requete)) {
					$not = '>';
					if (in_array($matches[2][$key], array('!=', '!IN'))) {
						$not = '==';
					}
					$array = var_export(explode(',', $matches[3][$key]), true);
					$condition = str_replace($matches[0][$key], "(count(array_intersect($val, $array)) $not 0)", $condition);
				}
			}
			// On vérifie que l'on a pas @toto@="valeur" qui fait planter l'eval(),
			// on annule cette condition dans ce cas pour éviter une erreur du type :
			// PHP Fatal error:  Can't use function return value in write context
			$type_condition = preg_replace('#@(.+)@#U', '', $condition_originale);
			if (trim($type_condition) != '=') {
				eval('$ok = '.$condition.';');
			}
			if (!$ok) {
				if ($remplissage_uniquement == false or is_null($env)) {
					unset($saisies[$cle]);
				}
				if (is_null($env)) {
					saisies_set_request_null_recursivement($saisie);
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
 * Vérifie qu'on ne tente pas de faire executer du code PHP en utilisant afficher_si.
 * N'importe quoi autorisé entre @@ et "" et ''
 * Liste de mot clé autorisé en dehors
 * @param string $condition
 * @return bool true si usage légitime, false si tentative d'execution de code PHP
 */
function saisies_verifier_securite_afficher_si($condition) {
	$autoriser_hors_guillemets = array("!", "IN", "\(", "\)", "=", "\s", "&&", "\|\|");
	$autoriser_hors_guillemets = "#(".implode($autoriser_hors_guillemets, "|").")#m";

	$entre_guillemets = "#(?<guillemet>(^\\\)?(\"|'|@))(.*)(\k<guillemet>)#mU"; // trouver tout ce qu'il y entre guillemet, sauf si les guillemets sont échapés
	$condition = preg_replace($entre_guillemets, "", $condition);//Supprimer tout ce qu'il y a entre guillement
	$condition = preg_replace($autoriser_hors_guillemets, "", $condition);//Supprimer tout ce qui est autorisé hors guillemets
	if ($condition) {//S'il restre quelque chose, c'est pas normal
		return false;
	}
	//Sinon c'est que c'est bon
	return true;
}

/**
 * Teste une condition d'afficher_si
 * @param string|array champ le champ à tester. Cela peut être :
 *	- un string
 *	- un tableau
 *	- un tableau sérializé
 * @param string $operateur : l'opérateur:
 *	- IN
 *	- !IN
 *	- ==
 *	- !=
 *	@param string $valeur la valeur à tester pour un IN. Par exemple "23" ou encore "23", "25"
 * @return bool false / true selon la condition
 **/
function saisies_tester_condition_afficher_si($champ, $operateur, $valeur) {
	// Dans tous les cas, enlever les guillemets qui sont au sein de valeur
	$valeur = str_replace("'", "", $valeur);
	$valeur = str_replace('"', "", $valeur);

	//Si champ est de type string, tenter d'unserializer
	$tenter_unserialize = @unserialize($champ);
	if ($tenter_unserialize)  {
		$champ = $tenter_unserialize;
	}

	//Et maintenant appeler les sous fonctions qui vont bien
	if (is_string($champ)) {
		return saisies_tester_condition_afficher_si_string($champ, $operateur, $valeur);
	}  elseif (is_array($champ)) {
		return saisies_tester_condition_afficher_si_array($champ, $operateur, $valeur);
	} else {
		return false;
	}
}

/**
 * Teste un condition d'afficher_si lorsqu'il s'agit d'une chaîne
 * @param string champ le champ à tester.
 * @param string $operateur : l'opérateur:
 *	- IN
 *	- !IN
 *	- ==
 *	- !=
 *	@param string $valeur la valeur à tester pour un IN. Par exemple "23" ou encore "23", "25"
 * @return bool false / true selon la condition
**/
function saisies_tester_condition_afficher_si_string($champ, $operateur, $valeur) {
	if ($operateur == "==") {
		return $champ == $valeur;
	} elseif ($operateur == "!=") {
		return $champ != $valeur;
	} else {//Si mauvaise operateur -> on annule
		return false;
	}
}

/**
 * Teste un condition d'afficher_si lorsqu'il s'agit d'un tableau
 * @param array champ le champ à tester.
 * @param string $operateur : l'opérateur:
 *	- IN
 *	- !IN
 *	- ==
 *	- !=
 * @param string $valeur la valeur à tester pour un IN. Par exemple "23" ou encore "23", "25"
 * @return bool false / true selon la condition
**/
function saisies_tester_condition_afficher_si_array($champ, $operateur, $valeur) {
	// Convertir les valeurs en vrai tableau
	$valeur = explode(",", $valeur);
	$intersection = array_intersect($champ, $valeur);
	if ($operateur == "==" or $operateur == "IN") {
		return count($intersection) > 0;
	} else {
		return count($intersection) == 0;
	}
	return false;
}
