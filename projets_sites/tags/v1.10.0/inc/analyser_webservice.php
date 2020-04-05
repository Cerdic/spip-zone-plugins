<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * On analyse le xml issu du plugin Info SPIP
 * ou respectant cette norme xml et on retourne le tableau correspondant
 * au formulaire de création d'un site de projets.
 *
 * @uses recuperer_page
 * @uses charger_fonction
 *
 * @param string $url
 *          URL du webservice
 *
 * @return array|bool $valeurs
 */
function inc_analyser_webservice_dist($url, $login = '', $password = '') {
	include_spip('iterateur/data');
	include_spip('inc/distant');
	$recuperer_flux = charger_fonction('recuperer_flux', 'inc');
	$convertir = charger_fonction('xml_to_array', 'inc');

	$valeurs = array();
	$page = $recuperer_flux($url, $login, $password);
	/* On vérifie qu'on a bien un content et que celui-ci n'est pas du code html */
	if (isset($page['content']) and preg_match("/<html>/", $page['content'])) {
		spip_log("La page $url ne renvoie pas un XML\nLogin : $login \nPassword : $password", 'projets_sites');
		return false;
	}
	/* On a bien un content, on peut maintenant l'analyser */
	$xml = $convertir($page['content']);
	$parse_url = parse_url($url);
	parse_str($parse_url['query'], $query);

	$valeurs['webservice'] = $url;

	if (isset($query['cle'])) {
		$valeurs['uniqid'] = $query['cle'];
	}

	if (is_array($xml)) {
		spip_log(print_r($xml, true), 'projets_sites');
		foreach ($xml as $key => $value) {
			switch ($key) {
				case 'nom_site':
					$valeurs['titre'] = $xml[$key]['value'];
					break;
				case 'logiciel':
					$valeurs['logiciel_nom'] = $xml[$key]['nom'];
					$valeurs['logiciel_version'] = $xml[$key]['version'];
					$valeurs['logiciel_revision'] = $xml[$key]['revision'];
					$valeurs['logiciel_charset'] = $xml[$key]['charset'];
					break;
				case 'date_creation':
					$valeurs['date_creation'] = $xml[$key]['value'];
					break;
				case 'fo':
					$valeurs['fo_url'] = $xml[$key]['url'];
					break;
				case 'bo':
					$valeurs['bo_url'] = $xml[$key]['url'];
					break;
				case 'type_site':
					$valeurs['type_site'] = $xml[$key]['value'];
					// Au cas où InfoSPIP n'est pas à jour sur le site cible.
					if ($xml[$key]['value'] == 'prod') {
						$valeurs['type_site'] = '07prod';
					} elseif ($xml[$key]['value'] == 'prep') {
						$valeurs['type_site'] = '06prep';
					} elseif ($xml[$key]['value'] == 'rec') {
						$valeurs['type_site'] = '05rec';
					} elseif ($xml[$key]['value'] == 'dev') {
						$valeurs['type_site'] = '02dev';
					}
					break;
				case 'applicatif':
					$valeurs['serveur_nom'] = $xml[$key]['nom'];
					$valeurs['serveur_path'] = $xml[$key]['path'];
					$valeurs['serveur_port'] = $xml[$key]['port'];
					$valeurs['serveur_logiciel'] = $xml[$key]['logiciel'];
					break;
				case 'apache':
					/* L'index 0 contient les modules du serveur */
					foreach ($xml[$key][0] as $key_module => $value_module) {
						$valeurs['apache_modules'][] = $value_module['nom'];
					}
					$valeurs['apache_modules'] = implode(', ', $valeurs['apache_modules']);
					break;
				case 'php':
					/* L'index 0 contient les extensions PHP du serveur */
					foreach ($xml[$key][0] as $key_extension => $value_extension) {
						$valeurs['php_extensions'][] = $value_extension['nom'];
					}
					$valeurs['php_extensions'] = implode(', ', $valeurs['php_extensions']);
					$valeurs['php_version'] = $xml[$key]['version'];
					$valeurs['php_memory'] = $xml[$key]['memory'];
					$valeurs['php_timezone'] = $xml[$key]['timezone'];
					break;
				case 'administrateurs':
					foreach ($xml[$key][0] as $admin) {
						$admin = array_filter($admin);
						$valeurs['auteurs_admin'][] = implode('|', $admin);
					}
					$valeurs['auteurs_admin'] = implode("\n", $valeurs['auteurs_admin']);
					// $valeurs['auteurs_admin_length'] = strlen($valeurs['auteurs_admin']);
					break;
				case 'webmestres':
					foreach ($xml[$key][0] as $superadmin) {
						$superadmin = array_filter($superadmin);
						/* on ne garde pas la clé 0 qui correspondrait au contenu de la balise */
						unset($superadmin[0]);
						$valeurs['auteurs_webmestres'][] = implode('|', $superadmin);
					}
					$valeurs['auteurs_webmestres'] = implode("\n", $valeurs['auteurs_webmestres']);
					// $valeurs['auteurs_webmestres_length'] = strlen($valeurs['auteurs_webmestres']);
					break;
				case 'plugins':
					foreach ($xml[$key][0] as $plugins) {
						// $plugins = array_filter($plugins);
						/* On ne garde pas la clé 0 qui correspondrait au contenu de la balise */
						unset($plugins[0]);
						$valeurs['logiciel_plugins'][] = implode('|', $plugins);
					}
					$valeurs['logiciel_plugins'] = implode("\n", $valeurs['logiciel_plugins']);
					break;
				case 'sgbd':
					/**
					 * On identifie les informations attendues pour le SGBD
					 * Le fait de les nommer et donc de les limiter évite de prendre
					 * des champs non prévus dans la table spip_projets_sites
					 */
					$sgbd_wanted = array('serveur', 'port', 'nom', 'type', 'prefixe', 'version', 'charset', 'collation');
					/* On utilise ces informations pour construire le tableau de valeurs de SGBD */
					if (is_array($xml[$key]) and count($xml[$key]) > 0) {
						spip_log("sgbd \n" . print_r($xml[$key], true), 'projets_sites');
						foreach ($xml[$key] as $sgbd_field => $sgbd_value) {
							/* Si l'index n'est pas dans les valeurs voulues `sgbd_wanted` et n'est pas un chiffre
							 * on construit notre index de $valeurs['sgbd_*']
							 */
							if (in_array($sgbd_field, $sgbd_wanted) and !is_int($sgbd_field)) {
								$valeurs['sgbd_' . $sgbd_field] = $sgbd_value;
							}
						}
					}
					break;
				default:
					# code..
					break;
			}
		}
	}
	ksort($valeurs);

	return $valeurs;
}

