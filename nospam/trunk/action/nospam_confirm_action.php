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
				// on move ce fichier, le temps de l'execution, pour eviter une double exec (les navs font parfois un double hit sur l'iframe)
				@rename($fichier_action, $fichier_action = $fichier_action . ".inprogress");

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
		// pas la peine de loger si le fichier .inprogress est la, c'est un autre thread qui s'en occupe
		if (!file_exists($fichier_action . '.inprogress')) {
			spip_log("nospam_confirm_action_dist:hash $hash errone: fichier $fichier_action absent (doublon deja execute ?)", 'nospam' . _LOG_ERREUR);
		}
	}

	// supprimer les actions plus vieilles que 5mn en les logeant
	$old_time = $_SERVER['REQUEST_TIME'] - 5 * 60;
	nospam_purge_actions($dir_actions, ['mtime' => $old_time, 'limit' => 100]);

	// et on renvoie un html minimum
	if (!_request('redirect')) {
		include_spip('inc/headers');
		http_status(204); // No Content
		header("Connection: close");
	}
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
 * @param string $method
 * @return string
 */
function nospam_confirm_action_prepare(
	$function,
	$description,
	$arguments = array(),
	$file = '',
	$time = null,
	$method = 'script') {

	// on stocke le descriptif de l'action a lancer dans un fichier
	$desc = [
		'function' => $function,
		'description' => $description,
		'arguments' => $arguments,
		'file' => $file,
		'time' => $time,
		'date' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), // pour les logs, on met la date de la demande d'action
	];
	$desc = json_encode($desc);

	$dir_actions = sous_repertoire(_DIR_TMP,_DIR_CONFIRM_ACTIONS);

	$hash = nospam_hash_action($desc);
	ecrire_fichier($dir_actions . $hash . ".json", $desc);

	include_spip('inc/actions');
	include_spip('inc/filtres');
	$url_action = str_replace("&amp;", "&", generer_action_auteur("nospam_confirm_action", $hash));
	$url_action_redirect = parametre_url($url_action, 'redirect', self(), '&');

	switch ($method) {
		case 'iframe':
			$title = attribut_html(_T('nospam:info_alt_antispam'));
			$html_action = "<iframe src='$url_action' width='1' height='1' style='display:inline-block;border:0;background:transparent;overflow:hidden;' title='$title'></iframe>";
			break;

		case 'script':
		default:

			$bouton_action = charger_filtre('bouton_action');
			$libelle = attribut_html(_T('nospam:libelle_je_ne_suis_pas_un_robot'));
			$html_action = $bouton_action($libelle, $url_action_redirect, 'btn-primary btn-sm btn-antispam');

			$js = "jQuery.ajax({url: '{$url_action}'}).done(function(){jQuery('.nospam-checkbox').addClass('checked');})";
			$html_action = "<span class='nospam-checkbox small'></span><script>$js</script><noscript>$html_action</noscript>";
			$css = file_get_contents(find_in_path('css/nospam-checkbox.min.css'));
			$html_action .= "<style type='text/css'>$css</style>";
			break;

	}


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


/**
 * Purge le répertoire des actions en logant tout ce qu'on purge
 *
 * @param string $dir
 *     Chemin du répertoire à purger
 * @param array $options
 *     Tableau des options. Peut être :
 *
 *     - atime : timestamp pour ne supprimer que les fichiers antérieurs
 *       à cette date (via fileatime)
 *     - mtime : timestamp pour ne supprimer que les fichiers antérieurs
 *       à cette date (via filemtime)
 *     - limit : nombre maximum de suppressions
 * @return int
 *     Nombre de fichiers supprimés
 **/
function nospam_purge_actions($dir, $options = array()) {
	if (!is_dir($dir) or !is_readable($dir)) {
		return;
	}
	$handle = opendir($dir);
	if (!$handle) {
		return;
	}

	$total = 0;

	while (($fichier = @readdir($handle)) !== false) {
		// Eviter ".", "..", ".htaccess", ".svn" etc.
		if ($fichier[0] == '.') {
			continue;
		}
		$chemin = "$dir/$fichier";
		if (is_file($chemin)) {
			if ((!isset($options['atime']) or (@fileatime($chemin) < $options['atime']))
				and (!isset($options['mtime']) or (@filemtime($chemin) < $options['mtime']))
			) {
				$action = file_get_contents($chemin);
				spip_log("Purge action non confirmee $fichier: $action", 'nospam_unconfirmed' . _LOG_INFO_IMPORTANTE);
				@unlink($chemin);
				$total++;
			}
		} else {
			if (is_dir($chemin)) {
				$opts = $options;
				if (isset($options['limit'])) {
					$opts['limit'] = $options['limit'] - $total;
				}
				$total += nospam_purge_actions($chemin, $opts);
				if (isset($options['subdir']) && $options['subdir']) {
					spip_unlink($chemin);
				}
			}
		}

		if (isset($options['limit']) and $total >= $options['limit']) {
			break;
		}
	}
	closedir($handle);

	return $total;
}
