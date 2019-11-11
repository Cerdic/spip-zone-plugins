<?php
/**
 * Plugin No-SPAM
 * (c) 2008-2019 Cedric Morin Yterium&Nursit
 * Licence GPL
 *
 */


define('_DIR_CONFIRM_ACTIONS', 'actions_a_confirmer');

/**
 * Traiter la confirmation d'action envoyee par le navigateur apres le POST du formulaire
 */
function action_nospam_confirm_action_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$hash = $securiser_action();

	$dir_actions = sous_repertoire(_DIR_TMP,_DIR_CONFIRM_ACTIONS);
	if (file_exists($fichier_action = $dir_actions. $hash . '.json')) {

		lire_fichier($fichier_action, $desc_json);
		if ($desc_json and $hash === nospam_hash_action($desc_json)) {

			if ($desc = json_decode($desc_json, true)) {
				// on purge ce fichier, tout de suite, l'action est imminente
				@unlink($fichier_action);

				// si on a fournit un time alors il faut ajouter l'action dans la queue
				if (!is_null($desc['time'])) {
					spip_log("nospam_confirm_action_dist $hash: ajout de l'action a job_queue $desc_json", 'nospam' . _LOG_DEBUG);
					job_queue_add($desc['function'], $desc['description'], $desc['arguments'], $desc['file'], false, $desc['time']);
				}
				else {
					$fonction = $desc['function'];
					if (strlen($inclure = trim($desc['file']))) {
						if (substr($inclure, -1) == '/') { // c'est un chemin pour charger_fonction
							$f = charger_fonction($fonction, rtrim($inclure, '/'), false);
							if ($f) {
								$fonction = $f;
							}
						} else {
							include_spip($inclure);
						}
					}

					if (!function_exists($fonction)) {
						spip_log("nospam_confirm_action_dist $hash: fonction $fonction ($inclure) inexistante $desc_json", 'nospam' . _LOG_ERREUR);
					}
					else {
						$res = call_user_func_array($fonction, $desc['arguments']);
						spip_log("nospam_confirm_action_dist $hash: execution de $fonction() $desc_json RES=" . var_export($res,true), 'nospam' . _LOG_DEBUG);
					}
				}
			}
			else {
				spip_log("nospam_confirm_action_dist:contenu action $desc_json invalide", 'nospam' . _LOG_ERREUR);
			}
		}
		else {
			spip_log("nospam_confirm_action_dist:hash $hash errone: contenu action $desc_json", 'nospam' . _LOG_ERREUR);
		}

		// dans tous les cas on purge ce fichier, l'action est faite ou impossible
		@unlink($fichier_action);
	}
	else {
		spip_log("nospam_confirm_action_dist:hash $hash errone: fichier $fichier_action absent (doublon deja execute ?)", 'nospam' . _LOG_ERREUR);
	}

	// supprimer les actions plus vieilles que 5mn
	include_spip('inc/invalideur');
	$old_time = $_SERVER['REQUEST_TIME'] - 5 * 60;
	purger_repertoire($dir_actions, ['mtime' => $old_time, 'limit' => 100]);

	// et on renvoie un html minimum
	include_spip('inc/actions');
	$out = "<html></html>";
	ajax_retour($out, false);
}


/**
 * Generer le HTML a afficher pour faire confirmer une action par l'utilisateur a son insu
 * (antispam qui declenche donc l'action uniquement si l'utilisateur charge les ressources de la page)
 *
 * @param string $function
 * @param string $description
 * @param array $arguments
 * @param string $file
 * @param null $time
 * @return string
 */
function nospam_confirm_action_prepare(
	$function,
	$description,
	$arguments = array(),
	$file = '',
	$time = null) {

	// on stocke le descriptif de l'action a lancer dans un fichier
	$desc = [
		'function' => $function,
		'description' => $description,
		'arguments' => $arguments,
		'file' => $file,
		'time' => $time
	];
	$desc = json_encode($desc);

	$dir_actions = sous_repertoire(_DIR_TMP,_DIR_CONFIRM_ACTIONS);

	$hash = nospam_hash_action($desc);
	ecrire_fichier($dir_actions . $hash . ".json", $desc);

	include_spip('inc/actions');
	include_spip('inc/filtres');
	$url_action = generer_action_auteur("nospam_confirm_action", $hash);
	$title = attribut_html(_T('nospam:info_alt_antispam'));
	$html_action = "<iframe src='$url_action' width='1' height='1' style='display:inline-block;border:0;background:transparent;overflow:hidden;' title='$title'></iframe>";

	return $html_action;
}


/**
 * Calculer un hash pour une action donnee
 *
 * @param $desc_json
 * @return bool|string
 */
function nospam_hash_action($desc_json) {
	if (!function_exists('secret_du_site')) {
		include_spip('inc/securiser_action');
	}
	$hash = substr(md5(__FILE__ . secret_du_site() . $desc_json), 0, 16);

	return $hash;
}