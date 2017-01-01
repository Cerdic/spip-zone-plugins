<?php

/**
* Gestion de l'affichage et traitement d'un formulaire Formidable
*
* @package SPIP\Formidable\Formulaires
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
return;
}

include_spip('inc/formidable');
include_spip('inc/formidable_fichiers');
include_spip('inc/saisies');
include_spip('base/abstract_sql');
include_spip('inc/autoriser');
include_spip('plugins/installer');

function formidable_id_formulaire($id) {
	// on utilise une static pour etre sur que si l'appel dans verifier() passe, celui dans traiter() passera aussi
	// meme si entre temps on perds la base
	static $id_formulaires = array();
	if (isset($id_formulaires[$id])) {
		return $id_formulaires[$id];
	}

	if (is_numeric($id)) {
		$where = 'id_formulaire = ' . intval($id);
	} elseif (is_string($id)) {
		$where = 'identifiant = ' . sql_quote($id);
	} else {
		return 0;
	}

	$id_formulaire = intval(sql_getfetsel('id_formulaire', 'spip_formulaires', $where));

	if ($id_formulaire
		and !test_espace_prive()
		and !objet_test_si_publie('formulaire', $id_formulaire)) {
		return $id_formulaires[$id] = 0;
	}

	return $id_formulaires[$id] = $id_formulaire;
}

/**
* Chargement du formulaire CVT de Formidable.
*
* Genere le formulaire dont l'identifiant (numerique ou texte est indique)
*
* @param int|string $id
*     Identifiant numerique ou textuel du formulaire formidable
* @param array $valeurs
*     Valeurs par défauts passées au contexte du formulaire
*     Exemple : array('hidden_1' => 3) pour que champ identifie "@hidden_1@" soit prerempli
* @param int|bool $id_formulaires_reponse
*     Identifiant d'une réponse pour forcer la reedition de cette reponse spécifique
*
* @return array
*     Contexte envoyé au squelette HTML du formulaire.
**/
function formulaires_formidable_charger($id, $valeurs = array(), $id_formulaires_reponse = false) {
	$contexte = array();

	// On peut donner soit un id soit un identifiant
	if (!$id_formulaire = formidable_id_formulaire($id)) {
		return;
	}

	// On cherche si le formulaire existe
	if ($formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = ' . intval($id_formulaire))) {
		// On ajoute un point d'entrée avec les infos de ce formulaire
		// pour d'eventuels plugins qui en ont l'utilité
		$contexte['_formidable'] = $formulaire;
		// Classes CSS 
		$contexte['_css'] = $formulaire['css'];

		// Est-ce que la personne a le droit de répondre ?
		if (autoriser('repondre', 'formulaire', $formulaire['id_formulaire'], null, array('formulaire' => $formulaire))) {
			$saisies = unserialize($formulaire['saisies']);
			$traitements = unserialize($formulaire['traitements']);

			// Si on est en train de réafficher les valeurs postées,
			// ne pas afficher les saisies hidden
			if ($formulaire['apres'] == 'valeurs'
				and _request('formidable_afficher_apres') == 'valeurs'
			) {
				foreach ($saisies as $k => $saisie) {
					if (isset($saisie['saisie'])
						and $saisie['saisie'] == 'hidden'
					) {
						unset($saisies[$k]);
					}
				}
			}

			// On déclare les champs avec les valeurs par défaut
			$contexte = array_merge(saisies_lister_valeurs_defaut($saisies), $contexte);
			$contexte['mechantrobot'] = '';
			// On ajoute le formulaire complet
			$contexte['_saisies'] = $saisies;

			$contexte['id'] = $formulaire['id_formulaire'];
			$contexte['_hidden'] = '<input type="hidden" name="id_formulaire" value="' . $contexte['id'] . '"/>';

			// S'il y a des valeurs par défaut dans l'appel, alors on pré-remplit
			if ($valeurs) {
				// Si c'est une chaine on essaye de la parser
				if (is_string($valeurs)) {
					$liste = explode(',', $valeurs);
					$liste = array_map('trim', $liste);
					$valeurs = array();
					foreach ($liste as $i => $cle_ou_valeur) {
						if ($i%2 == 0) {
							$valeurs[$liste[$i]] = $liste[$i+1];
						}
					}
				}

				// Si on a un tableau,
				// alors on écrase avec les valeurs données depuis l'appel
				if ($valeurs and is_array($valeurs)) {
					$contexte = array_merge($contexte, $valeurs);
				}
			}

			// Si on passe un identifiant de reponse, on edite cette reponse si elle existe
			if ($id_formulaires_reponse = intval($id_formulaires_reponse)) {
				$contexte = formidable_definir_contexte_avec_reponse($contexte, $id_formulaires_reponse, $ok);
				if ($ok == false) {
					$contexte['editable'] = false;
					$contexte['message_erreur'] = _T('formidable:traiter_enregistrement_erreur_edition_reponse_inexistante');
				}
			} else {
				// calcul des paramètres d'anonymisation
				$options = isset($traitements['enregistrement']) ? $traitements['enregistrement'] : null;

				$anonymisation = (isset($options['anonymiser']) && $options['anonymiser']==true)
					? isset($options['anonymiser_variable']) ? $options['anonymiser_variable'] : ''
					: '';

				// Si multiple = non mais que c'est modifiable, alors on va chercher
				// la dernière réponse si elle existe
				if ($options
					and !$options['multiple']
					and $options['modifiable']
					and $reponses = formidable_verifier_reponse_formulaire($formulaire['id_formulaire'], $options['identification'], $anonymisation)
				) {
					$id_formulaires_reponse = array_pop($reponses);
					$contexte = formidable_definir_contexte_avec_reponse($contexte, $id_formulaires_reponse, $ok);
				}
			}
		} else {
			$contexte['editable'] = false;
			// le formulaire a déjà été répondu.
			// peut être faut il afficher les statistiques des réponses
			if ($formulaire['apres']=='stats') {
				// Nous sommes face à un sondage auquel on a déjà répondu !
				// On remplace complètement l'affichage du formulaire
				// par un affichage du résultat de sondage !
				$contexte['_remplacer_formulaire'] = recuperer_fond('modeles/formulaire_analyse', array(
					'id_formulaire' => $formulaire['id_formulaire'],
				));
			} else {
				$contexte['message_erreur'] = _T('formidable:traiter_enregistrement_erreur_deja_repondu');
			}
		}
	} else {
		$contexte['editable'] = false;
		$contexte['message_erreur'] = _T('formidable:erreur_inexistant');
	}
	if (!isset($contexte['_hidden'])) {
		$contexte['_hidden'] = '';
	}
	$contexte['_hidden'] .= "\n" . '<input type="hidden" name="formidable_afficher_apres' /*.$formulaire['id_formulaire']*/ . '" value="' . $formulaire['apres'] . '"/>'; // marche pas

	if ($precharger= _request('_formidable_cvtupload_precharger_fichiers')) {
		$contexte['cvtupload_precharger_fichiers'] = $precharger;
	}
	$contexte['formidable_afficher_apres'] = $formulaire['apres'];

	return $contexte;
}


/**
* Vérification du formulaire CVT de Formidable.
*
* Pour chaque champ posté, effectue les vérifications demandées par
* les saisies et retourne éventuellement les erreurs de saisie.
*
* @param int|string $id
*     Identifiant numerique ou textuel du formulaire formidable
* @param array $valeurs
*     Valeurs par défauts passées au contexte du formulaire
*     Exemple : array('hidden_1' => 3) pour que champ identifie "@hidden_1@" soit prerempli
* @param int|bool $id_formulaires_reponse
*     Identifiant d'une réponse pour forcer la reedition de cette reponse spécifique
*
* @return array
*     Tableau des erreurs
**/
function formulaires_formidable_verifier($id, $valeurs = array(), $id_formulaires_reponse = false) {
$erreurs = array();

	// On peut donner soit un id soit un identifiant
	if (!$id_formulaire = formidable_id_formulaire($id)) {
		$erreurs['message_erreur'] = _T('formidable:erreur_base');
		} else {
		// Sale bête !
		if (_request('mechantrobot')!='') {
			$erreurs['hahahaha'] = 'hahahaha';
			return $erreurs;
		}

		$formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = ' . intval($id_formulaire));
		$saisies = unserialize($formulaire['saisies']);

		$erreurs_par_fichier = array(); 
		$erreurs = saisies_verifier($saisies,true,$erreurs_par_fichier);

		// On supprime de $_FILES les fichiers envoyés qui ne passent pas le test de vérification	
		
		//$infos_plugins = charger_fonction('infos_plugins','plugins');
		$plugins_actifs = liste_plugin_actifs();
		if (isset($plugins_actifs['CVTUPLOAD'])) {
			include_spip('inc/cvtupload');
			foreach ($erreurs as $champ => $erreur) {
					if (isset($erreurs_par_fichier[$champ])) {
						cvtupload_nettoyer_files_selon_erreurs($champ, $erreurs_par_fichier[$champ]);
						}
					}
			}

		// Si on a pas déjà une erreur sur le champ unicite, on lance une verification
		if ($formulaire['unicite'] != '') {
			if (!$erreurs[$formulaire['unicite']]) {
				$reponses = sql_allfetsel(
					'R.id_formulaire AS id',
					'spip_formulaires_reponses AS R
						LEFT JOIN spip_formulaires AS F
						ON R.id_formulaire=F.id_formulaire
						LEFT JOIN spip_formulaires_reponses_champs AS C
						ON R.id_formulaires_reponse=C.id_formulaires_reponse',
					'R.id_formulaire = ' . $id_formulaire . '
						AND C.nom='.sql_quote($formulaire['unicite']).'
						AND C.valeur='.sql_quote(_request($formulaire['unicite'])).'
						AND R.statut = "publie"'
				);
				if (is_array($reponses) && count($reponses) > 0) {
					$erreurs[$formulaire['unicite']] = $formulaire['message_erreur_unicite'] ? _T($formulaire['message_erreur_unicite']) : _T('formidable:erreur_unicite');
				}
			}
		}

		if ($erreurs and !isset($erreurs['message_erreur'])) {
			$erreurs['message_erreur'] = _T('formidable:erreur_generique');
		}
	}
	return $erreurs;
}


/**
 * Traitement du formulaire CVT de Formidable.
 *
 * Exécute les traitements qui sont indiqués dans la configuration des
 * traitements de ce formulaire formidable.
 *
 * Une fois fait, gère le retour après traitements des saisies en fonction
 * de ce qui a été configuré dans le formulaire, par exemple :
 * - faire réafficher le formulaire,
 * - faire afficher les saisies
 * - rediriger sur une autre page...
 *
 * @param int|string $id
 *     Identifiant numerique ou textuel du formulaire formidable
 * @param array $valeurs
 *     Valeurs par défauts passées au contexte du formulaire
 *     Exemple : array('hidden_1' => 3) pour que champ identifie "@hidden_1@" soit prerempli
 * @param int|bool $id_formulaires_reponse
 *     Identifiant d'une réponse pour forcer la reedition de cette reponse spécifique
 *
 * @return array
 *     Tableau des erreurs
 **/
function formulaires_formidable_traiter($id, $valeurs = array(), $id_formulaires_reponse = false) {
	$retours = array();

	// POST Mortem de securite : on log le $_POST pour ne pas le perdre si quelque chose se passe mal
	include_spip('inc/json');
	$post = json_encode(array("post"=>$_POST,"files"=>$_FILES));
	spip_log($post, 'formidable_post'._LOG_INFO_IMPORTANTE);

	// On peut donner soit un id soit un identifiant
	if (!$id_formulaire = formidable_id_formulaire($id)) {
		return array('message_erreur'=>_T('formidable:erreur_base'));
	}

	$formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = ' . $id_formulaire);
	$traitements = unserialize($formulaire['traitements']);
	$traitements = pipeline ('formidable_traitements', 
		array(
			'args'=>array('id_formulaire'=>$id_formulaire), 
			'data'=>$traitements
		)
	);
	// selon le choix, le formulaire se remet en route à la fin ou non
	$retours['editable'] = ($formulaire['apres']=='formulaire');
	$retours['formidable_afficher_apres'] = $formulaire['apres'];
	$retours['id_formulaire'] = $id_formulaire;

	// Si on a une redirection valide
	if (($formulaire['apres'] == 'redirige') and ($formulaire['url_redirect'] != '')) {
		refuser_traiter_formulaire_ajax();
		// traiter les raccourcis artX, brX
		include_spip('inc/lien');
		$url_redirect = typer_raccourci($formulaire['url_redirect']);
		if (count($url_redirect) > 2) {
			$url_redirect = $url_redirect[0] . $url_redirect[2];
		} else {
			$url_redirect = $formulaire['url_redirect']; // URL classique
		}

		$retours['redirect'] = $url_redirect;
	}

	// les traitements deja faits se notent ici
	// pour etre sur de ne pas etre appeles 2 fois
	// ainsi si un traitement A a besoin d'un traitement B,
	// et que B n'est pas fait quand il est appele, il peut rendre la main sans rien faire au premier coup
	// et sera rappele au second tour
	$retours['traitements'] = array();
	$erreur_texte = '';

	// Si on a des traitements
	if (is_array($traitements) and !empty($traitements)) {
		$maxiter = 5;
		do {
			foreach ($traitements as $type_traitement => $options) {
				// si traitement deja appele, ne pas le relancer
				if (!isset($retours['traitements'][$type_traitement])) {
					if ($appliquer_traitement = charger_fonction($type_traitement, 'traiter/', true)) {
						$retours = $appliquer_traitement(
							array(
								'formulaire' => $formulaire,
								'options' => $options,
								'id_formulaire' => $id_formulaire,
								'valeurs' => $valeurs,
								'id_formulaires_reponse' => $id_formulaires_reponse,
							),
							$retours
						);
					} else {
						// traitement introuvable, ne pas retenter
						$retours['traitements'][$type_traitement] = true;
					}
				}
			}
		} while (count($retours['traitements']) < count($traitements) and $maxiter--);
		// si on ne peut pas traiter correctement, alerter le webmestre
		if (count($retours['traitements']) < count($traitements)) {
			$erreur_texte = "Impossible de traiter correctement le formulaire $id\n"
				. 'Traitements attendus :'.implode(',', array_keys($traitements))."\n"
				. 'Traitements realises :'.implode(',', array_keys($retours['traitements']))."\n";
		}

		// Si on a personnalisé le message de retour, c'est lui qui est affiché uniquement
		if ($formulaire['message_retour']) {
			$retours['message_ok'] = _T_ou_typo($formulaire['message_retour']);
		}
	} else {
		$retours['message_erreur'] = _T('formidable:retour_aucun_traitement');
	}
	if (isset($retours['fichiers'])) {// traitement particuliers si fichiers
		if ($erreurs_fichiers = formidable_produire_messages_erreurs_fichiers($retours['fichiers'])) { // Inspecter les fichiers pour voir s'il y a des erreurs
			// Avertir l'utilisateur
			if (isset($retours['message_erreur'])) {
				$retours['message_erreur'] .= "<br />".$erreurs_fichiers['message_public'];
			} else {
				$retours['message_erreur'] = $erreurs_fichiers['message_public'];
			}
			// Avertir le webmestre
			if (isset($retours['id_formulaires_reponse'])){
				$erreur_fichiers_sujet = "[ERREUR] Impossible de sauvegarder les fichiers de la réponse ".$retours['id_formulaires_reponse']." au formulaire $id";
			} else {
				$erreur_fichiers_sujet = "[ERREUR] Impossible de sauvegarder les fichiers de la réponse au formulaire $id";
			}
			$erreur_fichiers_texte = "Récupérez le plus rapidement possible les fichiers temporaires suivants\n";
			$erreur_fichiers_texte .= $erreurs_fichiers['message_webmestre'];
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$envoyer_mail($GLOBALS['meta']['email_webmaster'], $erreur_fichiers_sujet, $erreur_fichiers_texte);

		}
		if ($formulaire['apres'] == 'valeurs') { // Si on affiche après les valeurs des réponses, modifier _request pour les saisies de types fichiers 
			$vignette_par_defaut = charger_fonction('vignette', 'inc/');
			foreach ($retours['fichiers'] as $saisie=>$description) {
				foreach ($description as $i => $desc){ // ajouter la vignette et l'url
					if (!isset($description[$i]['erreur'])) {
						$description[$i]['vignette'] = $vignette_par_defaut($desc['extension'],false);
						if (isset($retours['id_formulaires_reponse'])) {// si réponse enregistrée
							$description[$i]['url'] =  formidable_generer_url_action_recuperer_fichier($id_formulaire, $retours['id_formulaires_reponse'], $saisie, $desc['fichier']);
						} elseif (isset($retours['timestamp'])) { // si réponse simplement envoyée par courriel
							$description[$i]['url'] = formidable_generer_url_action_recuperer_fichier_email($saisie, 
								$desc['fichier'], 
								array('timestamp'=>$retours['timestamp'])
							);
						}
					}
				}	
				set_request($saisie, $description);
			}
		}
	}
	// lorsqu'on affichera à nouveau le html, dire à cvt-upload de ne pas générer le html pour les résultats des saisies fichiers
	if ($formulaire['apres']=='formulaire' and isset($retours['fichiers'])) {
		$formidable_cvtupload_precharger_fichiers = array();
		set_request('_fichiers', null);
		set_request('_cvtupload_precharger_fichiers_forcer',true);
		foreach ($retours['fichiers'] as $champ => $valeur){
			$i = -1;
			foreach ($valeur as $id=>$info){
				$i++;
				if (isset ($info['fichier'])) {
					$nom_fichier = $info['fichier'];
				} else {
					$nom_fichier = $info['nom'];
				}
				if (isset($retours['id_formulaires_reponse'])) {
					$chemin_fichier = _DIR_FICHIERS_FORMIDABLE
						."formulaire_".$retours['id_formulaire']
						."/reponse_".$retours['id_formulaires_reponse']
						."/".$champ
						."/".$nom_fichier;
					$formidable_cvtupload_precharger_fichiers[$champ][$i]['url'] = formidable_generer_url_action_recuperer_fichier($retours['id_formulaire'], $retours['id_formulaires_reponse'], $champ, $nom_fichier);
					$formidable_cvtupload_precharger_fichiers[$champ][$i]['chemin'] = $chemin_fichier;
				} elseif (isset($retours['timestamp'])) {
					$chemin_fichier = _DIR_FICHIERS_FORMIDABLE
						."timestamp/"
						.$retours['timestamp']."/"
						.$champ."/"
						.$nom_fichier;
					$formidable_cvtupload_precharger_fichiers[$champ][$i]['chemin'] = $chemin_fichier; 
					$formidable_cvtupload_precharger_fichiers[$champ][$i]['url'] = formidable_generer_url_action_recuperer_fichier_email(
						$champ, 
						$nom_fichier,
						array('timestamp'=>$retours['timestamp'])
					);
				}
			}
		}
		set_request('_formidable_cvtupload_precharger_fichiers', $formidable_cvtupload_precharger_fichiers);
	}
	// si aucun traitement, alerter le webmestre pour ne pas perdre les donnees
	if (!$erreur_texte and !count($retours['traitements'])) {
		$erreur_texte = "Aucun traitement pour le formulaire $id\n";
	}
	if ($erreur_texte) {
		$erreur_sujet = "[ERREUR] Traitement Formulaire $id";
		// dumper la saisie pour ne pas la perdre
		$erreur_texte .= "\n".var_export($_REQUEST, true)."\n".var_export($_FILES, true);
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
		$envoyer_mail($GLOBALS['meta']['email_webmaster'], $erreur_sujet, $erreur_texte);
	}
	unset($retours['traitements']);
	return $retours;
}

/**
 * Déclare à cvtupload les champs fichiers du formulaire
 *
 * @param int|string $id
 *     Identifiant numerique ou textuel du formulaire formidable
 * @param array $valeurs
 *     Valeurs par défauts passées au contexte du formulaire
 *     Exemple : array('hidden_1' => 3) pour que champ identifie "@hidden_1@" soit prerempli
 * @param int|bool $id_formulaires_reponse
 *     Identifiant d'une réponse pour forcer la reedition de cette reponse spécifique
 *
 * @return array
 *     Tableau des champs de type fichier
 **/
function formulaires_formidable_fichiers($id, $valeurs = array(), $id_formulaires_reponse = false) {
	// On peut donner soit un id soit un identifiant
	if (!$id_formulaire = formidable_id_formulaire($id)) {
		return array();
	}

	// On cherche les saisies du formulaire
	if ($saisies = sql_getfetsel('saisies', 'spip_formulaires', 'id_formulaire = ' . intval($id_formulaire))) {
		$saisies = unserialize($saisies);
		include_spip('inc/saisies_lister');
		$saisies_fichiers = array_keys(saisies_lister_avec_type($saisies,'fichiers'));
		return $saisies_fichiers; 
	}
}
/**
 * Ajoute dans le contexte les elements
 * donnés par une reponse de formulaire indiquée
 *
 * @param array $contexte
 *     Contexte pour le squelette HTML du formulaire
 * @param int $id_formulaires_reponse
 *     Identifiant de réponse
 * @param bool $ok
 *     La reponse existe bien ?
 * @return array $contexte
 *     Contexte complète des nouvelles informations
 *
 **/
function formidable_definir_contexte_avec_reponse($contexte, $id_formulaires_reponse, &$ok) {

	// On prépare des infos si jamais on a des champs fichiers
	$saisies_fichiers = saisies_lister_avec_type($contexte['_saisies'], 'fichiers');// les saisies de type fichier
	$fichiers = array();
	$id_formulaire = $contexte['_formidable']['id_formulaire'];

	// On va chercher tous les champs
	$champs = sql_allfetsel(
		'nom, valeur',
		'spip_formulaires_reponses_champs',
		'id_formulaires_reponse = ' . $id_formulaires_reponse
	);
	$ok = count($champs) ? true : false;
	// On remplit le contexte avec les résultats précédents
	foreach ($champs as $champ) {
		if (array_key_exists($champ['nom'], $saisies_fichiers)) {
			$valeur= unserialize($champ['valeur']);
			$nom = $champ['nom'];
			$fichiers[$nom] = array();
			$chemin = _DIR_FICHIERS_FORMIDABLE
				."formulaire_$id_formulaire/reponse_$id_formulaires_reponse/"
				."$nom/";
			foreach ($valeur as $f => $fichier) {
				$fichiers[$nom][$f]= array();
				$param = serialize(array(
					'formulaire' => $id_formulaire,
					'reponse' => $id_formulaires_reponse,
					'fichier' => $fichier['nom'],
					'saisie' => $champ['nom']
				));
				$fichiers[$nom][$f]['url'] =  formidable_generer_url_action_recuperer_fichier($id_formulaire, $id_formulaires_reponse, $champ['nom'], $fichier['nom']); 
				$fichiers[$nom][$f]['chemin'] = $chemin.$fichier['nom'];
			}
		} else {
			$test_array = filtre_tenter_unserialize_dist($champ['valeur']);
			$contexte[$champ['nom']] = is_array($test_array) ? $test_array : $champ['valeur'];
		}
	}
	if ($fichiers != array()) {//s'il y a des fichiers dans les réponses 
		$contexte['cvtupload_precharger_fichiers'] = $fichiers;
	}
	return $contexte;
}

/**
 * Produire un message d'erreur concaténant les messages d'erreurs
 * par fichier.
 * Fournir également une forme pour l'envoyer par webmestre
 * @param array $fichiers le tableau des fichiers qui a été remplie par formidable_deplacer_fichiers_produire_vue_saisie()
 * @return array ('message_public' => 'message', 'message_webmestre' => 'message'
**/
function formidable_produire_messages_erreurs_fichiers($fichiers) {
	$message_public = '';
	$message_webmestre = '';
	foreach ($fichiers as $champ => $description_champ) {
		foreach ($description_champ as $n => $description) {
			if (isset($description['erreur'])) {
				$message_public .= $description['erreur']."\n";
				$message_webmestre .= "Pour le champ $champ[$n]:\n"
					. '- Le fichier temporaire : '.$description['tmp_name']."\n"
					. '- Ayant pour véritable nom : '.$description['nom']." \n"; 
			}
		}
	}
	if ($message_public !='') {
		return array('message_public'=>$message_public, 'message_webmestre'=>$message_webmestre);
	} else {
		return '';
	}
}

