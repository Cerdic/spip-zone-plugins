<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction qui recherche la presence d'un lien et de sa liaison dans la table spip_linkchecks
 *
 * @param string $url
 * 	URL que l'on recherche
 * @param int $id_obj
 *   Identifiant de l'objet attaché
 * @param string $type
 *   Type de l'objet attaché
 * @return array
 *   tableau associatif avec une clé 'etat' 3 valeur possible
 *   - 0 : le lien n'a pas été trouvé
 *   - 1 : le lien a été trouvé mais il n'est pas rattaché à l'objet
 * 	 - 2 : le lien a été trouvé et il est bien rattaché à l'objet
 * 	 Si la clé "etat" est égale à 1, le tableau indique par la clé "id" l'identifiant du lien
 */
function linkcheck_tester_presence_lien($url, $id_obj, $type, $publie) {
	$retour = array('etat'=>0);
	// on recherche le lien par l'URL
	$id_linkcheck = sql_getfetsel('id_linkcheck', 'spip_linkchecks', 'url = '.sql_quote($url));
	if ($id_linkcheck) {
		// si on l'a trouvé on verifie si est attaché à l'objet passé en paramatre
		$sel = sql_fetsel(
			'id_linkcheck, publie',
			'spip_linkchecks_liens',
			'id_linkcheck='.$id_linkcheck.' AND id_objet='.intval($id_obj).' AND objet='.sql_quote($type)
		);
		if (!$sel) {
			$retour['etat'] = 1;
			$retour['id'] = $id_linkcheck;
		} else {
			$retour['etat']= 2;
			if ($publie != $sel['publie']) {
				sql_updateq(
					'spip_linkchecks_liens',
					array('publie' => $publie),
					'id_linkcheck='.$id_linkcheck.' AND id_objet='.intval($id_obj).' AND objet='.sql_quote($type)
				);
				$statut_linkckeck = sql_getfetsel('publie', 'spip_linkchecks', 'id_linkcheck = '.intval($id_linkcheck));
				if (!$statut_linkckeck or $publie != $statut_linkckeck) {
					if (!$statut_linkckeck or $statut_linkckeck == '' or $publie== 'oui') {
						sql_updateq(
							'spip_linkchecks',
							array('publie' => $publie),
							'id_linkcheck='.intval($id_linkcheck)
						);
					} else if (!sql_getfetsel('publie', 'spip_linkchecks_liens', 'id_linkcheck='.$id_linkcheck.'  AND publie="oui"')) {
						sql_updateq(
							'spip_linkchecks',
							array('publie' => $publie),
							'id_linkcheck='.intval($id_linkcheck)
						);
					}
				}
			}
		}
	}
	return $retour;
}

/**
 * Fonction qui liste les liens dans un texte ou un tableau
 *
 * @param string|array $champs
 * 		Une chaine de caractere ou un tableau de chaine de caractere
 * 		contenant les liens
 * @return array
 * 		La liste des liens trouvé dans la variable $champs
 */
function linkcheck_lister_liens($champs) {
	include_spip('inc/lien');
	$liens = array(); //tableau des liens pour detection des liens supprimés dans l'objet

	if (!is_array($champs)) {
		$champs = array('texte' => $champs);
	}

	/**
	 * TODO : trouver une regexp mieux que cela et complète
	 * @var string $classe_alpha
	 */
	$classe_alpha = 'a-zA-Z0-9\x{00a1}-\x{FFFF}\(\)';
	$tab_expreg = array(
		"('|\"| |\.|\->|\]|,|;|\s)(((((http|https|ftp|ftps)://)?www\.)|((http|https|ftp|ftps)://(?:\S+(?::\S*)?@)?.([".$classe_alpha."'\-]*\.)?))(['".$classe_alpha."'0-9\-\+]*\.)+([a-zA-Z0-9]{2,9})(?::\d{2,5})?(/[".$classe_alpha."=.?&~_;\-\+\@\:\,/%#]*)?)('|\"| |\.|,|;|\s|\|\->])?",
		'(\->)([a-zA-Z]{3,10}[0-9]{1,})\]'
	);

	foreach ($champs as $champ_value) {
		$champ_value=trim($champ_value);
		if (!empty($champ_value)) {
			$tab_temp = array();
			// trouvé les URLs
			foreach ($tab_expreg as $expreg) {
				if (preg_match_all('`'.$expreg.'`u', ' '.$champ_value.' ', $matches) > 0) {
					foreach ($matches[2] as $cle => $m) {
						if (!empty($m) && !in_array($matches[11][$cle], array('invalid', 'test', 'localhost', 'example'))) {
							$tab_temp[]= rtrim(rtrim(rtrim($m, '.'), ','), '->');
						}
					}
				}
			}

			// Ajout du prefix http:// si necessaire
			foreach ($tab_temp as &$url_site) {
				$temp=$url_site;
				if (preg_match('#^([a-zA-Z0-9\-]*\.)([a-zA-Z0-9\-]*\.)#', $temp)) {
					$url_site = 'http://' . $url_site;
				}
			}
			// Ajout au tableau du lien
			$url_site = trim($url_site);
			if (!empty($tab_temp)) {
				$tab_temp = array_unique($tab_temp);
				$liens = array_unique(array_merge($liens, $tab_temp));
			}
		}
	}
	return $liens;
}


/**
 * Fonction qui ajoute les liens dans la base
 *
 * @param array $tab_liens
 * 		Tableau de liens à ajouter
 * @param string $type_objet
 * 		Type de l'objet à lier
 * @param int $id_objet
 * 		Identifiant de l'objet à lier
 *
 * @return array $ret
 */
function linkcheck_ajouter_liens($tab_liens, $objet, $id_objet, $publie = 'non') {
	foreach ($tab_liens as $lien) {
		// on teste si c'est un lien interne ou externe
		$distant = (strpos($lien, '.')) ? true : false;
		// on test son existence dans la base
		$exi = linkcheck_tester_presence_lien($lien, $id_objet, $objet, $publie);
		//s'il existe
		if ($exi['etat'] > 0) {
			if ($exi['etat'] == 1) {
				//on l'ajoute ds la table de liaison
				$ins = sql_insertq(
					'spip_linkchecks_liens',
					array (
						'id_linkcheck' => $exi['id'],
						'id_objet'=>$id_objet,
						'objet'=>$objet,
						'publie' => $publie
					)
				);
				$publie_linkcheck = sql_getfetsel('publie', 'spip_linkchecks', 'id_linkcheck = '.intval($exi['id']));
				if ($publie_linkcheck != $publie) {
					if ($publie_linkcheck == 'non') {
						sql_updateq('spip_linkchecks', array('publie' => 'oui'), 'id_linkcheck = '.intval($exi['id']));
					} else {
						$ok = sql_countsel('spip_linkchecks_liens', 'publie = "oui" AND id_linkcheck = '.intval($exi['id']));
						if (!$ok or $ok == 0) {
							sql_updateq('spip_linkchecks', array('publie' => 'non'), 'id_linkcheck = '.intval($exi['id']));
						}
					}
				}
			}
		//s'il existe pas
		} else {
			//on l'insere dans la base des url
			$ins = sql_insertq(
				'spip_linkchecks',
				array('url'=>$lien, 'distant'=>$distant, 'date'=> date('Y-m-d H:i:s'), 'publie' => $publie)
			);
			//et ds la base qui lie un url a un objet
			sql_insertq(
				'spip_linkchecks_liens',
				$donnees = array(
					'id_linkcheck' => $ins,
					'id_objet' => $id_objet,
					'objet' => $objet,
					'publie' => $publie
				)
			);
		}
	}
}

/**
 * Retourne le statut de l'url externe
 *
 * @param string $url
 * 		L'url externe à tester
 * @return array $ret
 */
function linkcheck_tester_lien_externe($url) {
	include_spip('inc/linkcheck_vars');
	$ret = array();
	$tabStatus = linkcheck_etats_liens();

	if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
		$url = 'http://'.$url;
	}

	$ret['etat'] = $tabStatus[0][4];
	$ret['code'] = 'no-code';

	/**
	 * Fixer le timeout d'une page à 30
	 * Faire croire que l'on est un navigateur normal (pas un bot)
	 */
	$contexte = array(
			'http' => array(
				'timeout' => 30,
				'header' => "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.16 (KHTML, like Gecko) Chrome/24.0.1304.0 Safari/537.16\r\n".
							'Accept-Encoding: gzip, deflate'
			),
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
			)
		);
	if (!defined('_INC_DISTANT_USER_AGENT')) {
		define('_INC_DISTANT_USER_AGENT', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.16 (KHTML, like Gecko) Chrome/24.0.1304.0 Safari/537.16');
	}

	stream_context_set_default(
		$contexte
	);
	$head = @get_headers($url);

	if (!$head) {
		$head = linkcheck_get_headers($url);
	}

	if ($head) {
		if (preg_match('`[0-9]{3}`', $head[0], $status)) {
			$ret['etat'] = isset($tabStatus[0][$status[0][0]]) ? $tabStatus[0][$status[0][0]] : 'malade';
			$ret['code'] = $status[0];
			$redirections = array();
			$codes = array();
			if (is_array($head) && $ret['etat'] == 'deplace') {
				foreach ($head as $line) {
					if (preg_match('/Location/Uims', $line, $matches)) {
						$redirections[] = parametre_url(parametre_url(parametre_url(trim(str_replace(array('Content-Location:', 'Content-location:', 'content-location:', 'Location:', 'location:'), '', $line)), 'utm_source', ''), 'utm_medium', ''), 'utm_campaign', '');
					}
					if (preg_match('/HTTP\/1\.1 (404)/Uims', $line, $status)) {
						$ret['etat'] = 'mort';
						$ret['code'] = $status[1];
						unset($ret['redirection']);
					}
				}
				$redirections = array_reverse($redirections);
				foreach ($redirections as $redirection) {
					$end_redirection = '';
					if (strpos($redirection, 'http://') === false && strpos($redirection, 'https://') === false) {
						$end_redirection = $redirection;
					} else {
						$url_finale = parse_url($redirection);
						if (strlen($end_redirection) > 0) {
							$redirection = str_replace($url_finale['query'], '', str_replace($url_finale['path'], '', $redirection));
							$ret['redirection'] = rtrim($redirection, '/').'/'.$end_redirection;
						} else {
							$ret['redirection'] = $redirection;
						}

						$domaine = rtrim(str_replace($url_finale['query'], '', str_replace($url_finale['path'], '', $ret['redirection'])), '?');
						/**
						 * Cas où c'est une redirection chez nous depuis un site externe (bit.ly...)
						 */
						if (str_replace(array('https://', 'http://', '//'), '', rtrim($domaine, '/')) == str_replace(array('https://', 'http://', '//'), '', rtrim($GLOBALS['meta']['adresse_site'], '/'))) {
							$redir_chez_nous = str_replace($domaine, '', $ret['redirection']);
							include_spip('inc/urls');
							$url_dans_site = urls_decoder_url($redir_chez_nous);
							if (is_array($url_dans_site) && isset($url_dans_site[0])) {
								$ret['redirection'] = $url_dans_site[0].$url_dans_site[1][id_table_objet($url_dans_site[0])];
							}
						}
						break;
					}
					/**
					 * Si pas de redirection ou la redirection n'a pas de http... et que l'on a un end_redirection
					 */
					if ((!isset($ret['redirection']) or strpos($ret['redirection'], '//') === false) && strlen($end_redirection) > 0) {
						$url_finale = parse_url($url);
						$domaine = rtrim(str_replace($url_finale['query'], '', str_replace((($url_finale['path'] == '/') ? '' : $url_finale['path']), '', $url)), '?');
						$domaine = rtrim($domaine, '/');
						/**
						 * Soit on est sur notre propre domaine et dans ce cas on essaie de retrouver
						 * l'url de l'objet si possible pour faire un lien interne
						 * Sinon on récupère le domaine de l'url d'origine
						 */
						if (str_replace(array('https://', 'http://', '//'), '', $domaine) == rtrim(str_replace(array('https://', 'http://', '//'), '', $GLOBALS['meta']['adresse_site']), '/')) {
							include_spip('inc/urls');
							$url_dans_site = urls_decoder_url($end_redirection);
							if (is_array($url_dans_site) && isset($url_dans_site[0])) {
								$ret['redirection'] = $url_dans_site[0].$url_dans_site[1][id_table_objet($url_dans_site[0])];
							} else {
								$ret['redirection'] = $domaine.'/'.ltrim($end_redirection, '/');
							}
						} else {
							$ret['redirection'] = $domaine.'/'.ltrim($end_redirection, '/');
						}
					}
				}
			}
		} else {
			$statut = 200;
			$ret['code'] = $statut;
			$ret['etat'] = isset($tabStatus[0][$statut]) ? $tabStatus[0][$statut] : 'malade';
		}
	}
	return $ret;
}

/**
 *
 * @param string $url
 * @return boolean|unknown
 */
function linkcheck_get_headers($url) {
	include_spip('inc/distant');
	list($f, $fopen) = init_http('GET', $url, false);

	if (!$f) {
		spip_log("ECHEC init_http $url", 'linkcheck.'._LOG_ERREUR);
		return false;
	}
	$headers = recuperer_entetes($f, '');
	return $headers;
}

/**
 * Retourne le statut de l'objet ciblé par $url
 *
 * @param string $url
 * 		L'url interne à tester
 *
 * @return array $ret
 */
function linkcheck_tester_lien_interne($url) {
	include_spip('inc/lien');
	include_spip('base/objets');
	include_spip('inc/linkcheck_vars');
	$ret = array();
	$tabStatus = linkcheck_etats_liens();

	if (strpos($url, '#') === 0) {
		$ret['etat'] = $tabStatus[1]['publie'];
		$ret['code'] = 'publie';
	}

	$rac = typer_raccourci($url);

	if (count($rac) && !empty($rac[0]) && !empty($rac[2])) {
		$type_objet = $rac[0];
		$id_objet = $rac[2];
		$objet = objet_type(table_objet($type_objet));
		if (objet_test_si_publie($objet, $id_objet)) {
			$ret['etat'] = 'ok';
			$ret['code'] = 200;
			return $ret;
		}
		$table_sql = table_objet_sql($type_objet);
		$nom_champ_id = id_table_objet($type_objet);
		$statut_objet = sql_getfetsel('statut', $table_sql, $nom_champ_id.'='.$id_objet);

		if (!empty($statut_objet)) {
			if ($type_objet != 'auteur') {
				$ret['etat'] = isset($tabStatus[1][$statut_objet]) ? $tabStatus[1][$statut_objet] : 'malade';
				$ret['code'] = $statut_objet;
			} else {
				$ret['etat'] = $tabStatus[1]['publie'];
				$ret['code'] = 'publie';
			}
		} else {
			$ret['etat'] =  $tabStatus[1]['poubelle'];
			$ret['code'] = 'poubelle';
		}
	} else {
		$ret['etat'] = $tabStatus[1]['poubelle'];
		$ret['code'] = 'poubelle';
	}

	return $ret;
}

/**
 * Fonction de verifications des liens rescencés
 *
 * Les liens stockés dans la table spip_linkchecks sont verifiés
 * suivant leurs statuts, ou pas, par lots, ou pas.
 *
 * @param string $etatLien
 *	 L'url interne à tester
 * @param string $etatLien
 *	 L'url interne à tester
 * @param string $id
 *	 Identifiant d'un lien à verfier
 *
 * @return void
 */
function linkcheck_tests($cron = false, $etat = null, $id = 0) {
	$cpt = 0;
	include_spip('inc/config');
	$dil = lire_config('linkcheck_dernier_id_lien', '0');
	$where =($id) ? 'id_linkcheck='.intval($id) : 'id_linkcheck > '.$dil;
	$where .= (is_null($etat)) ? ' AND etat='.sql_quote($etat) : '';
	$limit = $cron ? lire_config('linkcheck/nb_verifs_cron', 30) : 5;
	/**
	 * On estime qu'au maximum, on accorde 1 minute 30 max pour tester chaque lien
	 * On essaie de forcer l'execution max de script en multipliant donc $limit par 90
	 */
	set_time_limit($limit*90);
	$sql = sql_allfetsel('*', 'spip_linkchecks', $where, '', 'etat, id_linkcheck ASC', '0,'.$limit);
	foreach ($sql as $res) {
		linkcheck_maj_etat($res);
		ecrire_config('linkcheck_dernier_id_lien', $res['id_linkcheck']);
		$cpt++;
		if ($cpt > $limit) {
			break;
		}
	}
	ecrire_config('linkcheck_dernier_id_lien', 0);
}


/**
 * Fonction qui parcourt les liens d'un objet afin de les insérer ds la base
 * et qui peut retourner un tableau des liens
 *
 * @param array $flux
 * @param array $ret
 */
function linkcheck_maj_etat($res) {
	//si le champ est inférieur à 6
	if (isset($res['essais']) && $res['essais'] < 6) {
		//on incrémente le champ
		sql_updateq('spip_linkchecks', array('essais' => $res['essais']++), 'id_linkcheck='.intval($res['id_linkcheck']));
		$test = ($res['distant'] == 1) ?
			linkcheck_tester_lien_externe($res['url']) : linkcheck_tester_lien_interne($res['url']);
	} else {
		//on abandonne les essais
		include_spip('inc/linkcheck_vars');
		$tabStatus = linkcheck_etats_liens();
		$test['etat'] = $tabStatus[0][1];
		$test['code'] = '119';
		sql_updateq('spip_linkchecks', array('essais' => 0), 'id_linkcheck='.intval($res['id_linkcheck']));
	}

	//sinon on le signale comme malade
	sql_updateq(
		'spip_linkchecks',
		array(
			'etat' => $test['etat'],
			'code' => $test['code'],
			'redirection' => isset($test['redirection']) ? $test['redirection'] : '',
			'maj' => date('Y-m-d H:i:s')
		),
		'id_linkcheck='.intval($res['id_linkcheck'])
	);
}
