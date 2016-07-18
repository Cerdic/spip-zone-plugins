<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Recuperer email et arg dans l'action confirm/subscribe/unsubscribe
 * en gerant les cas foireux introduits par les urls coupees dans les mails
 * ou par les services d'envoi+redirection qui abiment les URLs
 *
 * @param string $action
 * @return array
 */
function mailsubscribers_verifier_args_action($action) {
	$email = _request('email');
	$arg = _request('arg');

	if (is_null($arg) OR is_null($email)) {
		$query = $_SERVER["QUERY_STRING"];
		// cas du arg coupe
		if (strpos($query, "arg%") !== false) {
			$query = str_replace("arg%", "arg=", $query);
		}
		// cas du & transorme en &amp;
		if (strpos($query, '&amp;') !== false) {
			$query = str_replace("&amp;", "&", $query);
		}
		parse_str($query, $args);
		$arg = strtolower($args['arg']);
		$email = $args['email'];
		if (strlen($arg) > 40) {
			$arg = substr($arg, -40);
		}
		if ($arg AND $email) {
			spip_log("mailsubscriber : query_string mal formee, verifiez votre service d'envoi de mails [" . $_SERVER["QUERY_STRING"] . "]",
				"mailsubscribers" . _LOG_INFO_IMPORTANTE);
		}
	}
	if (!$email OR !$arg){
		spip_log(_request('action')." : (email,arg)=($email,$arg) non trouves", "mailsubscribers"._LOG_ERREUR);
		return false;
	}

	if (!$email
		OR !$row = sql_fetsel('id_mailsubscriber,email,jeton,lang,statut', 'spip_mailsubscribers', 'email=' . sql_quote($email) . ' OR email=' . sql_quote(mailsubscribers_obfusquer_email($email)))
	) {
		spip_log(_request('action')." : email $email pas dans la base spip_mailsubscribers", "mailsubscribers"._LOG_INFO_IMPORTANTE);
		return false;
	} else {
		include_spip("inc/lang");
		changer_langue($row['lang']);
		
		$identifiant = "";
		// verifier la cle telle quelle => generique, applicable pour toutes les listes
		$cle = mailsubscriber_cle_action($action, $row['email'], $row['jeton']);
		if ($arg !== $cle) {
			$subscriptions = sql_allfetsel('*', 'spip_mailsubscriptions', 'id_mailsubscriber=' . intval($row['id_mailsubscriber']));
			foreach ($subscriptions as $subscription){
				// verifier la cle pour cette liste
				$cle = mailsubscriber_cle_action($action, $row['email'], $row['jeton'] . '+' . $subscription['id_mailsubscribinglist']);
				if ($arg == $cle) {
					$identifiant = sql_getfetsel('identifiant', 'spip_mailsubscribinglists', 'id_mailsubscribinglist=' . intval($subscription['id_mailsubscribinglist']));
					break;
				}
			}
			// pas de correspondance => cle incorrecte
			if (!$identifiant){
				spip_log(_request('action')." : cle $arg incorrecte pour email $email", "mailsubscribers"._LOG_INFO_IMPORTANTE);
				return false;
			}
		}
	}

	return array($email, $identifiant);
}

/**
 * Normaliser le nom d'une liste de diffusion
 *
 * @param string $liste
 * @param string $category
 * @return string
 */
function mailsubscribers_normaliser_nom_liste($liste = '', $category = "newsletter") {
	$category = strtolower(trim(preg_replace(",\W,", "", $category)));

	if (!$liste) {
		return "$category";
	} // valeur fixe en cas de reantrance

	if (strpos($liste, "::") !== false) {
		$liste = explode("::", $liste);

		return mailsubscribers_normaliser_nom_liste($liste[1], $liste[0]);
	}
	include_spip("inc/charsets");
	$liste = translitteration($liste);
	$liste = strtolower($liste);

	$liste = trim(preg_replace(",[^\w-],", "", $liste));

	return $liste;
}

function mailsubscribers_obfusquer_email($email) {
	return md5($email) . "@example.org";
}

function mailsubscribers_test_email_obfusque($email) {
	return preg_match(",^[a-f0-9]+@example\.org$,", $email);
}

function mailsubscribers_obfusquer_mailsubscriber($id_mailsubscriber) {
	$row = sql_fetsel('*', 'spip_mailsubscribers', 'id_mailsubscriber=' . intval($id_mailsubscriber));
	if ($row
		and in_array($row['statut'], array('refuse', 'poubelle'))
		and !mailsubscribers_test_email_obfusque($row['email'])
	) {
		include_spip('inc/autoriser');
		include_spip('action/editer_objet');
		autoriser_exception("modifier", "mailsubscriber", $id_mailsubscriber);
		objet_modifier("mailsubscriber", $id_mailsubscriber,
			array('email' => mailsubscribers_obfusquer_email($row['email'])));
		autoriser_exception("modifier", "mailsubscriber", $id_mailsubscriber, false);
	}
}

/**
 * Mise en forme de la trace des abonnements/desabonnements dans le champ optin
 *
 * @param string $actions
 *   nouvelles actions tracees
 * @param string $trace
 *   trace existante
 * @return string
 */
function mailsubscribers_trace_optin($actions, $trace) {
	$trace = trim($trace);
	$trace .=
		"\n"
		. trim($actions) . ' : '
		. date('Y-m-d H:i:s') . " "
		. _T('public:par_auteur') . ''
		. (isset($GLOBALS['visiteur_session']['id_auteur']) ? "#" . $GLOBALS['visiteur_session']['id_auteur'] . ' ' : '')
		. (isset($GLOBALS['visiteur_session']['nom']) ? $GLOBALS['visiteur_session']['nom'] . ' ' : '')
		. (isset($GLOBALS['visiteur_session']['session_nom']) ? $GLOBALS['visiteur_session']['session_nom'] . ' ' : '')
		. (isset($GLOBALS['visiteur_session']['session_email']) ? $GLOBALS['visiteur_session']['session_email'] . ' ' : '')
		. '(' . $GLOBALS['ip'] . ')';

	return $trace;
}

/**
 * Compter les inscrits a une liste
 *
 * @param string $liste
 * @param string $statut
 * @return array|int
 */
function mailsubscribers_compte_inscrits($liste, $statut = 'valide') {
	static $count = null;

	if (is_null($count) OR isset($GLOBALS['mailsubscribers_recompte_inscrits'])) {
		$count = array();
		$rows = sql_allfetsel('id_mailsubscribinglist,statut,count(id_mailsubscriber) as n', 'spip_mailsubscriptions', '',
			'id_mailsubscribinglist,statut');

		// recuperer les correspondance id_mailsubscribinglist <=> identifiant
		$ids = array_map('reset', $rows);
		$listes = sql_allfetsel('id_mailsubscribinglist,identifiant', 'spip_mailsubscribinglists',
			sql_in('id_mailsubscribinglist', $ids));
		$ids = array();
		foreach ($listes as $l) {
			$ids[$l['id_mailsubscribinglist']] = $l['identifiant'];
		}

		foreach ($rows as $row) {
			$l = $ids[$row['id_mailsubscribinglist']];
			if (!isset($count[$l][$row['statut']])) {
				$count[$l][$row['statut']] = 0;
			}
			$count[$l][$row['statut']] += $row['n'];
		}

		// pour le compte sans liste, on prends le statut des mailsubscribers
		$rows = sql_allfetsel('statut,count(id_mailsubscriber) as n', 'spip_mailsubscribers', '', 'statut');
		foreach ($rows as $row) {
			if (!isset($count[''][$row['statut']])) {
				$count[''][$row['statut']] = 0;
			}
			$count[''][$row['statut']] += $row['n'];
		}

	}

	if ($statut == 'all') {
		if (isset($count[$liste])) {
			return $count[$liste];
		}

		return array();
	}
	if (isset($count[$liste][$statut])) {
		return $count[$liste][$statut];
	}

	return 0;
}

/**
 * Trouver une fonction de synchronisation pour une liste donnee
 * mailsubscribers_synchro_list_xxxx
 *
 * @param $liste
 * @return mixed|string
 */
function mailsubscribers_trouver_fonction_synchro($liste) {
	$f = mailsubscribers_normaliser_nom_liste($liste);
	$f = 'newsletter_' . $f;
	include_spip("public/parametrer"); // fichier mes_fonctions.php
	if (function_exists($f = "mailsubscribers_synchro_list_$f")) {
		return $f;
	}

	return "";
}

/**
 * Informer un subscriber : ici juste l'url unsubscribe a calculer
 *
 * @param array $infos
 * @return array mixed
 */
function mailsubscribers_informe_subscriber($infos) {
	static $identifiants;
	$infos['listes'] = array();
	$infos['subscriptions'] = array();
	if (isset($infos['id_mailsubscriber'])) {
		$infos['status'] = 'off';
		if (is_null($identifiants)) {
			$identifiants = array();
			$rows = sql_allfetsel('id_mailsubscribinglist,identifiant', 'spip_mailsubscribinglists');
			foreach ($rows as $row) {
				$identifiants[$row['id_mailsubscribinglist']] = $row['identifiant'];
			}
		}
		$subs = sql_allfetsel('id_mailsubscribinglist,statut', 'spip_mailsubscriptions', 'id_mailsubscriber=' . intval($infos['id_mailsubscriber']));
		foreach ($subs as $sub) {
			if (isset($identifiants[$sub['id_mailsubscribinglist']])) {
				$id = $identifiants[$sub['id_mailsubscribinglist']];
				$status = 'off';
				if ($sub['statut'] == 'valide') {
					$infos['listes'][] = $id;
					$status = 'on';
					$infos['status'] = 'on';
				} elseif (in_array($sub['statut'], array('prepa', 'prop'))) {
					$status = 'pending';
					if ($infos['status'] == 'off') {
						$infos['status'] = 'pending';
					}
				}
				$url_unsubscribe = mailsubscriber_url_unsubscribe($infos['email'], $infos['jeton'] . "+" . $sub['id_mailsubscribinglist']);
				$infos['subscriptions'][$id] = array(
					'id' => $id,
					'status' => $status,
					'url_unsubscribe' => $url_unsubscribe
				);
			}
		}
		unset($infos['id_mailsubscriber']);
	}

	// URL unscubscribe generale (a toutes les inscriptions)
	$infos['url_unsubscribe'] = mailsubscriber_url_unsubscribe($infos['email'], $infos['jeton']);

	unset($infos['jeton']);

	return $infos;
}

/**
 * Filtrer une liste a partir de sa category
 *
 * @param $liste
 * @param string $category
 * @return string
 *   chaine vide si la liste n'est pas dans la category
 *   nom de la liste sans le prefix de la category si ok
 */
function mailsubscribers_filtre_liste($liste, $category = "newsletter") {
	if (strncmp($liste, "$category::", $l = strlen("$category::")) == 0) {
		return substr($liste, $l);
	}

	return '';
}

/**
 * Renvoi les listes de diffusion disponibles avec leur status
 * (open,close,?)
 *
 * @param array $options
 *   status : filtrer les listes sur le status
 * @return array
 *   array
 *     id : identifiant
 *     titre : titre de la liste
 *     descriptif : descriptif de la liste
 *     status : status de la liste
 */
function mailsubscribers_listes($options = array()) {
	$filtrer_status = false;
	if (isset($options['status'])) {
		$filtrer_status = $options['status'];
	}
	if ($filtrer_status == 'open') {
		$filtrer_status = 'ouverte';
	}
	if ($filtrer_status == 'close') {
		$filtrer_status = 'fermee';
	}

	$where = array();
	$where[] = 'statut!=' . sql_quote('poubelle');
	if ($filtrer_status) {
		$where[] = 'statut=' . sql_quote($filtrer_status);
	}
	$rows = sql_allfetsel('identifiant as id,titre,descriptif,statut as status', 'spip_mailsubscribinglists', $where, '',
		'statut DESC,0+titre,titre');
	$listes = array();
	foreach ($rows as $row) {
		if ($row['status'] == 'ouverte') {
			$row['status'] = 'open';
		}
		if ($row['status'] == 'fermee') {
			$row['status'] = 'close';
		}
		$listes[$row['id']] = $row;
	}

	return $listes;
}


/**
 * Lance la synchro avec une liste en appelant la fonction
 * mailsubscribers_synchro_list_xxxx pour la liste des abonnes
 * puis la fonction de synchronisation
 *
 * @param $liste
 */
function mailsubscribers_do_synchro_list($liste) {
	if ($f = mailsubscribers_trouver_fonction_synchro($liste)) {
		$abonnes = $f();
		if (is_array($abonnes)
			AND (!count($abonnes) OR ($r = reset($abonnes) AND isset($r['email'])))
		) {
			$n = count($abonnes);
			spip_log("Synchronise liste $liste avec $n abonnes (fonction $f)", "mailsubscribers");
			mailsubscribers_synchronise_liste($liste, $abonnes);
		} else {
			spip_log("Synchronise liste $liste : abonnes mal formes en retour de la fonction $f",
				"mailsubscribers" . _LOG_ERREUR);
		}
	}
}


/**
 * Retourner la liste des abonnes qu'on veut voir dans la liste newsletter::0minirezo
 *
 * @return array
 */
function mailsubscribers_synchro_list_newsletter_0minirezo() {
	$auteurs = sql_allfetsel("email,nom", "spip_auteurs", "statut=" . sql_quote("0minirezo"));

	return $auteurs;
}

/**
 * Retourner la liste des abonnes qu'on veut voir dans la liste newsletter::1comite
 *
 * @return array
 */
function mailsubscribers_synchro_list_newsletter_1comite() {
	$auteurs = sql_allfetsel("email,nom", "spip_auteurs", "statut=" . sql_quote("1comite"));

	return $auteurs;
}

/**
 * Retourner la liste des abonnes qu'on veut voir dans la liste newsletter::6forum
 *
 * @return array
 */
function mailsubscribers_synchro_list_newsletter_6forum() {
	$auteurs = sql_allfetsel("email,nom", "spip_auteurs", "statut=" . sql_quote("6forum"));

	return $auteurs;
}


/**
 * Synchroniser les abonnes d'une liste en base avec un tableau fourni
 * TODO : permettre de fournir une resource SQL en entree et ne pas manipuler de gros tableau en memoire (robustesse)
 *
 * @param string $liste
 *   liste avec laquelle on synchronise les abonnes
 * @param array $abonnes
 *   chaque abonne est un tableau avec l'entree 'email' et les entrees optionnelles 'nom' et 'prenom'
 * @param array $options
 *   bool addonly : pour ajouter uniquement les nouveaux abonnes, et ne desabonner personne
 *   bool graceful : pour ne pas reabonner ceux qui se sont desabonnes manuellement
 */
function mailsubscribers_synchronise_liste($liste, $abonnes, $options = array()) {
	$listes = array($liste);
	$id_mailsubscribinglist = sql_getfetsel('id_mailsubscribinglist', 'spip_mailsubscribinglists',
		'identifiant=' . sql_quote($liste));
	if (!$id_mailsubscribinglist) {
		return;
	}

	if (is_bool($options)) {
		$options = array('addonly' => $options);
	}
	$options = array_merge(array('addonly' => false, 'graceful' => true), $options);

	$abonnes_emails = array();
	while (count($abonnes)) {
		$abonne = array_shift($abonnes);
		if (isset($abonne['email'])
			AND strlen($e = trim($abonne['email']))
		) {
			$abonnes_emails[$e] = $abonne;
		}
	}

	$subscribe = charger_fonction('subscribe', 'newsletter');
	$unsubscribe = charger_fonction('unsubscribe', 'newsletter');

	// d'abord on prend la liste de tous les abonnes en base
	// et on retire ceux qui ne sont plus dans le tableau $abonnes
	$subs = sql_allfetsel('S.email',
		'spip_mailsubscribers as S JOIN spip_mailsubscriptions as L ON S.id_mailsubscriber=L.id_mailsubscriber',
		'L.id_mailsubscribinglist=' . intval($id_mailsubscribinglist) . ' AND L.statut=' . sql_quote('valide'));
	spip_log("mailsubscribers_synchronise_liste $liste: " . count($subs) . " abonnes deja dans la liste",
		"mailsubscribers" . _LOG_DEBUG);
	foreach ($subs as $sub) {
		// OK il est toujours dans les abonnes
		if (isset($abonnes_emails[$sub['email']])) {
			unset($abonnes_emails[$sub['email']]);
		} // il n'est plus dans les abonnes on l'enleve sauf si flag $addonly==true
		elseif (!$options['addonly']) {
			//echo "unsubscribe ".$sub['email']."<br />";
			$unsubscribe($sub['email'], array('listes' => $listes, 'notify' => false));
		}
	}

	spip_log("mailsubscribers_synchronise_liste $liste: " . count($abonnes_emails) . " a abonner dans la liste",
		"mailsubscribers" . _LOG_DEBUG);
	// si il reste du monde dans $abonnes, c'est ceux qui ne sont pas en base
	// on les subscribe
	foreach ($abonnes_emails as $email => $abonne) {
		//echo "subscribe ".$email."<br />";
		$nom = (isset($abonne['nom']) ? $abonne['nom'] . ' ' : '');
		$nom .= (isset($abonne['prenom']) ? $abonne['prenom'] . ' ' : '');
		$subscribe($email, array(
			'nom' => trim($nom),
			'listes' => $listes,
			'force' => true,
			'notify' => false,
			'graceful' => $options['graceful'],
		));
	}

	// baisser les drapeaux edition de tout ce qu'on vient de faire
	if (function_exists('debloquer_tous')) {
		$id_a = (isset($GLOBALS['visiteur_session']['id_auteur']) ? $GLOBALS['visiteur_session']['id_auteur'] : $GLOBALS['ip']);
		debloquer_tous($id_a);
	}
}
