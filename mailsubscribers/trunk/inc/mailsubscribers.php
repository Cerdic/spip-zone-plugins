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
 *   email, $id_mailsubscribinglists
 */
function mailsubscribers_verifier_args_action($action) {
	$email = _request('email');
	$arg = _request('arg');

	// reparer le arg ou le retrouver dans la QUERY_STRING si jamais il était coupé
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
		if ($p = strpos($arg, '-') === false) {
			if (strlen($arg) > 40) {
				$arg = substr($arg, -40);
			}
		}
		else {
			// faisons un preg_match pour retrouver tous les elements malgre le caca qui a ete ajoute
			if (preg_match(",^(\d+-)+[0-9a-f]{40},", $arg, $m)) {
				$arg = $m[0];
			}
			else {
				$arg = null;
			}
		}
		// cas des urls qui ont ete triplement encodees : il faut encore urldecoder deux fois l'email
		// %2540 = urlencode(urlencode('@'))
		if (strpos($email, '%2540') !== false) {
			$email = urldecode($email);
		}
		// cas des urls qui ont ete doublement encodees : il faut encore urldecoder une fois l'email
		// %40 = urlencode('@')
		if (strpos($email, '%40') !== false) {
			$email = urldecode($email);
		}
		if ($arg AND $email) {
			spip_log("mailsubscriber : $email|$arg reconnus malgre la query_string mal formee (verifiez votre service d'envoi de mails) [" . $_SERVER["QUERY_STRING"] . "]", "mailsubscribers" . _LOG_INFO_IMPORTANTE);
		}
	}
	if (!$email OR !$arg){
		spip_log(_request('action')." : (email,arg)=($email,$arg) non trouves [" . $_SERVER["QUERY_STRING"] . "]", "mailsubscribers"._LOG_ERREUR);
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
		
		$identifiants = array();
		$jeton = $row['jeton'];
		$id_mailsubscribinglists = explode('-', $arg);
		array_pop($id_mailsubscribinglists); // le hash
		if (!$id_mailsubscribinglists) {
			$id_mailsubscribinglists = null;
		}
		// verifier la cle telle quelle
		// => soit une cle generique, applicable pour toutes les listes
		// => soit une cle avec des id_mailsubscribinglists
		$cle = mailsubscriber_cle_action($action, $email, $jeton, $id_mailsubscribinglists);
		// si elle ne match pas et que arg ne contient aucun $id_mailsubscribinglists cherchons si c'est un hash ancien format
		// avec juste une liste ajoute sous la forme jeton+id_mailsubscribinglist
		if ($arg !== $cle and is_null($id_mailsubscribinglists)) {
			$id_mailsubscribinglists = array();
			// on ne cherche que dans les subscriptions connues pour cet inscrit, sinon rien a faire
			$subscriptions = sql_allfetsel('*', 'spip_mailsubscriptions', 'id_mailsubscriber=' . intval($row['id_mailsubscriber']));
			foreach ($subscriptions as $subscription){
				// verifier la cle pour cette liste
				$cle = mailsubscriber_cle_action($action, $email, $jeton . '+' . $subscription['id_mailsubscribinglist']);
				if ($arg == $cle) {
					$id_mailsubscribinglists[] = $subscription['id_mailsubscribinglist'];
					break;
				}
			}
			// pas de correspondance => cle incorrecte
			if (!$subscription['id_mailsubscribinglist']){
				spip_log(_request('action')." : cle $arg incorrecte pour email $email", "mailsubscribers"._LOG_INFO_IMPORTANTE);
				return false;
			}
		}
	}

	return array($email, $id_mailsubscribinglists);
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
	} // valeur fixe en cas de re-entrance

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

function mailsubscribers_obfusquer_email($email, $force = false) {
    if (!$force and defined('_MAILSUBSCRIBERS_GARDER_EMAILENCLAIR') ) {
	    return $email;
    }

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
 * @param int $id_segment
 * @return array|int
 */
function mailsubscribers_compte_inscrits($liste, $statut = 'valide', $id_segment=0) {
	static $count = null;

	if (is_null($count) OR isset($GLOBALS['mailsubscribers_recompte_inscrits'])) {
		$count = array();
		$rows = sql_allfetsel('id_mailsubscribinglist,statut,id_segment,count(id_mailsubscriber) as n', 'spip_mailsubscriptions', '',
			'id_mailsubscribinglist,statut,id_segment');

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
			if (!isset($count[$l])) {
				$count[$l] = array();
			}
			if (!isset($count[$l][$row['statut']])) {
				$count[$l][$row['statut']] = array();
			}
			if (!isset($count[$l][$row['statut']][$row['id_segment']])) {
				$count[$l][$row['statut']][$row['id_segment']] = 0;
			}
			$count[$l][$row['statut']][$row['id_segment']] += $row['n'];
		}

		// pour le compte sans liste, on prends le statut des mailsubscribers
		$rows = sql_allfetsel('statut,count(id_mailsubscriber) as n', 'spip_mailsubscribers', '', 'statut');
		foreach ($rows as $row) {
			if (!isset($count[''][$row['statut']])) {
				$count[''][$row['statut']] = array(0=>0);
			}
			$count[''][$row['statut']][0] += $row['n'];
		}
		// sauf pour statut en attente : on compte tous les subscribers qui ont une inscription en attente
		$count['']['prop'][0] = sql_getfetsel('count(DISTINCT id_mailsubscriber)','spip_mailsubscriptions', 'statut='.sql_quote('prop').' AND id_segment=0');

	}

	if ($statut == 'all') {
		if (isset($count[$liste])) {
			return $count[$liste];
		}

		return array();
	}
	if (isset($count[$liste][$statut][$id_segment])) {
		return $count[$liste][$statut][$id_segment];
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
 * Recuperer la declaration des informations liees
 * prend nativement en charge 
 *   - les champs extras sur mailsubscribers
 *   - les mots cles associes aux mailsubscribers 
 * @pipeline mailsubscriber_informations_liees
 * @return array
 */
function mailsubscriber_declarer_informations_liees() {
	static $declaration;
	if (is_null($declaration)){

		$infos = array(
			'lang' => array(
				'titre' => $s = _T('mailsubscriber:label_lang'),
				/* Optionnel : utile si on veut uniquement afficher mais pas fournir de saisie pour les segments
 				'valeurs' => array(
					'id' => 'titre'
				),
				*/
			  // declaration de la saisie de cette info pour la creation de segments
			  'saisie' => 'selection',
			  'options' => array(
				  'label' => $s,
				  'nom' => 'lang',
				  'datas' => array()
			  ),
				'auto_field' => true, // internal : pour remplissage automatique a partir de la table
			)
		);
		// les valeurs des langues
		$langues = $GLOBALS['meta']['langues_proposees'];
		$langues = explode(',',$langues);
		foreach ($langues as $langue){
			$infos['lang']['options']['datas'][$langue] = traduire_nom_langue($langue);
		}

		// des champs extras ?
		if (test_plugin_actif('cextras')) {
			$saisies_tables = pipeline('declarer_champs_extras', array());
			if (isset($saisies_tables['spip_mailsubscribers'])) {
				foreach ($saisies_tables['spip_mailsubscribers'] as $saisie){
					$champ = array(
						'titre' => $saisie['options']['label'],
						'saisie' => $saisie['saisie'],
						'options' => $saisie['options'],
						'auto_field' => true, // internal : pour remplissage automatique a partir de la table
					);
					$id = $saisie['options']['nom'];
					$infos[$id] = $champ;
				}
			}
		}

		// des groupes de mots ?
		$groupes = sql_allfetsel('*','spip_groupes_mots','tables_liees like '.sql_quote('%mailsubscribers%'));
		foreach ($groupes as $groupe) {
			$infos['groupemots_'.$groupe['id_groupe']] = array(
				'titre' => $groupe['titre'],
				'saisie' => 'mot',
				'options' => array(
					'id_groupe' => $groupe['id_groupe'],
					'forcer_select' => 'oui',
					'nom' => 'groupemots_'.$groupe['id_groupe'],
					'label' => $groupe['titre'],
					'class' => 'chosen',
				),
				'auto_mot' => true, // internal : pour remplissage automatique a partir de liens mots-mailsubscribers
			);
		}

		// Appeler le pipeline avec declarer=true
		// data contient une entree par type d'information, avec les entrees titre et valeurs
		// + la declaration de saisies si option saisable pour la definition d'un segment de liste
		$flux = array(
			'args' => array(
				'declarer' => true,
			),
			'data' => $infos
		);
		$declaration = pipeline('mailsubscriber_informations_liees', $flux);
	}
	return $declaration;
}

/**
 * Recuperer les informations liees a un subscriber, utiles pour la segmentation
 * @pipeline mailsubscriber_informations_liees
 * @param $id_mailsubscriber
 * @param $email
 * @return array|mixed|null
 */
function mailsubscriber_recuperer_informations_liees($id_mailsubscriber, $email){
	$infos = array();
	if ($declaration = mailsubscriber_declarer_informations_liees()) {

		$row = null;
		foreach ($declaration as $nom => $champ){
			if (isset($champ['auto_field']) and $champ['auto_field']){
				if (is_null($row)) {
					$row = sql_fetsel('*','spip_mailsubscribers','id_mailsubscriber='.intval($id_mailsubscriber));
				}
				$infos[$nom] = $row[$nom];
			}
			elseif (isset($champ['auto_mot']) and $champ['auto_mot']
			  and strncmp($nom,'groupemots_',11)==0
			  and $id_groupe = $champ['options']['id_groupe']){
				$mots = sql_allfetsel('M.id_mot','spip_mots as M JOIN spip_mots_liens as L on L.id_mot=M.id_mot','L.objet='.sql_quote('mailsubscriber').' AND L.id_objet='.intval($id_mailsubscriber).' AND M.id_groupe='.intval($id_groupe));
				foreach($mots as $mot) {
					if (!isset($flux['data'][$nom])){
						$infos[$nom] = array();
					}
					$infos[$nom][] = $mot['id_mot'];
				}
			}
		}

		// Appeler avec la reference du subscriber
		$flux = array(
			'args' => array(
				'email' => $email,
				'id_mailsubscriber' => $id_mailsubscriber
			),
			'data' => $infos
		);

		$infos = pipeline('mailsubscriber_informations_liees', $flux);
	}
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
 * Les listes à la poubelles sont exclues sauf si on filtre par statut.
 *
 * @param array $options
 *   status : filtrer les listes sur le status
 *            (array|string) tableau ou liste de status séparés par des virgules
 *            ouverte | fermee | poubelle
 *   id : filtrer les listes selon leurs identifiants
 *        (array|string) tableau ou liste d'identifiants séparés par des virgules
 *   segments : fournir le detail des segments avec un identifant de la forme xxxx+nn
 * @return array
 *   array
 *     id : identifiant
 *     titre : titre de la liste
 *     descriptif : descriptif de la liste
 *     status : status de la liste
 *     from_name : nom de l'envoyeur (optionnel)
 *     from_email : nom de l'envoyeur (optionnel)
 */
function mailsubscribers_listes($options = array()) {
	// option : filtrer par statut
	$filtrer_status = array();
	if (!empty($options['status'])) {
		$filtrer_status = is_array($options['status']) ? $options['status'] : explode(',', $options['status']);
		// pour compatibilité descendante
		foreach ($filtrer_status as $k => $status){
			if ($status == 'open') {
				$filtrer_status[$k] = 'ouverte';
			}
			if ($status == 'close') {
				$filtrer_status[$k] = 'fermee';
			}
		}
	}
	// option : filtrer par identifiant
	$filtrer_id = array();
	if (!empty($options['id'])) {
		$filtrer_id = is_array($options['id']) ? $options['id'] : explode(',', $options['id']);
	}

	$where = array();
	if ($filtrer_status) {
		$where[] = sql_in('statut', $filtrer_status);
	} else {
		$where[] = 'statut != ' . sql_quote('poubelle');
	}
	if ($filtrer_id) {
		$where[] = sql_in('identifiant', $filtrer_id);
	}
	$rows = sql_allfetsel('identifiant as id,titre,descriptif,statut as status,adresse_envoi_nom as from_name,adresse_envoi_email as from_email,segments', 'spip_mailsubscribinglists', $where, '',
		'statut DESC,0+titre,titre');
	$listes = array();
	foreach ($rows as $row) {
		$segments = $row['segments'];
		unset($row['segments']);
		if ($row['status'] == 'ouverte') {
			$row['status'] = 'open';
		}
		if ($row['status'] == 'fermee') {
			$row['status'] = 'close';
		}
		$listes[$row['id']] = $row;
		if (isset($options['segments']) AND $options['segments']){
			$segments = unserialize($segments);
			if ($segments AND count($segments)){
				$id = $row['id'];
				$t = $row['titre'];
				foreach ($segments as $id_segment=>$segment){
					$row['id'] = $id . '+' . $id_segment;
					$row['titre'] = '&nbsp;— '.$t . ' > ' . $segment['titre'];
					$listes[$row['id']] = $row;
				}
			}
		}
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
			if (!mailsubscribers_synchronise_liste($liste, $abonnes)) {
				job_queue_add(
					"mailsubscribers_do_synchro_list",
					"Synchro liste (poursuivre) " . $liste,
					array($liste),
					"inc/mailsubscribers"
				);
			}
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
 * @return bool
 *   - true : indique que la synchronisation s’est terminée (ou qu’il n’y avait rien à faire)
 *   - false : indique que la synchronisation ne s’est pas terminée à cause d’un timeout proche
 */
function mailsubscribers_synchronise_liste($liste, $abonnes, $options = array()) {
	$listes = array($liste);
	$id_mailsubscribinglist = sql_getfetsel('id_mailsubscribinglist', 'spip_mailsubscribinglists',
		'identifiant=' . sql_quote($liste));
	if (!$id_mailsubscribinglist) {
		spip_log("Mailing liste $liste introuvable pour synchro", 'mailsubscribers');
		return true;
	}

	$timeout = ini_get('max_execution_time');
	// valeur conservatrice si on a pas reussi a lire le max_execution_time
	if (!$timeout) {
		$timeout = 30;
	} // parions sur une valeur tellement courante ...
	$max_time = time() + $timeout / 2;

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
	// -> on retire du tableau $abonnes ceux qui le sont deja
	// -> on desabonne ceux qui ne sont plus dans le tableau $abonnes
	$subs = sql_allfetsel('S.email',
		'spip_mailsubscribers as S JOIN spip_mailsubscriptions as L ON S.id_mailsubscriber=L.id_mailsubscriber',
		'L.id_mailsubscribinglist=' . intval($id_mailsubscribinglist) . ' AND L.id_segment=0 AND L.statut=' . sql_quote('valide'));
	spip_log("mailsubscribers_synchronise_liste $liste: " . count($subs) . " abonnes deja dans la liste",
		"mailsubscribers" . _LOG_DEBUG);
	$i=0;
	foreach ($subs as $sub) {
		// OK il est toujours dans les abonnes
		if (isset($abonnes_emails[$sub['email']])) {
			unset($abonnes_emails[$sub['email']]);
		} // il n'est plus dans les abonnes on l'enleve sauf si flag $addonly==true
		elseif (!$options['addonly']) {
			//echo "unsubscribe ".$sub['email']."<br />";
			$unsubscribe($sub['email'], array('listes' => $listes, 'notify' => false, 'remove' => true));
			$i++;
		}
		if (time() >= $max_time) {
			spip_log("mailsubscribers_synchronise_liste $liste: $i desabonnes de la liste mais temps ecoule", "mailsubscribers" . _LOG_DEBUG);
			return false;
		}
	}
	spip_log("mailsubscribers_synchronise_liste $liste: $i desabonnes de la liste", "mailsubscribers" . _LOG_DEBUG);
	unset($subs);

	// on enleve de la liste ceux qui ont deja ete abonnes dans le passe mais se sont desinscrit car on ne les reabonnera pas
	$obfusques = array_keys($abonnes_emails);
	$obfusques = array_combine(array_map('mailsubscribers_obfusquer_email', $obfusques), $obfusques);
	$unsubs = sql_allfetsel('S.email',
		'spip_mailsubscribers as S JOIN spip_mailsubscriptions as L ON S.id_mailsubscriber=L.id_mailsubscriber',
		'L.id_mailsubscribinglist=' . intval($id_mailsubscribinglist) . ' AND L.id_segment=0 AND L.statut=' . sql_quote('refuse') . ' AND ' . sql_in('S.email', array_merge(array_keys($obfusques), array_values($obfusques))));
	spip_log("mailsubscribers_synchronise_liste $liste: " . count($unsubs) . " ne veulent pas etre reabonnes a la liste", "mailsubscribers" . _LOG_DEBUG);
	foreach ($unsubs as $unsub) {
		if (isset($abonnes_emails[$unsub['email']])) {
			unset($abonnes_emails[$unsub['email']]);
		}
		elseif (isset($abonnes_emails[$obfusques[$unsub['email']]])) {
			unset($abonnes_emails[$obfusques[$unsub['email']]]);
		}
	}

	spip_log("mailsubscribers_synchronise_liste $liste: " . count($abonnes_emails) . " a abonner dans la liste", "mailsubscribers" . _LOG_DEBUG);
	// si il reste du monde dans $abonnes, c'est ceux qui ne sont pas en base
	// on les subscribe
	$i=0;
	foreach ($abonnes_emails as $email => $abonne) {
		//echo "subscribe ".$email."<br />";
		$nom = (isset($abonne['nom']) ? $abonne['nom'] . ' ' : '');
		$nom .= (isset($abonne['prenom']) ? $abonne['prenom'] . ' ' : '');
		$data_subscriber = array(
			'nom' => trim($nom),
			'listes' => $listes,
			'force' => true,
			'notify' => false,
			'graceful' => $options['graceful'],
		);
		if (isset($abonne['lang'])) {
			$data_subscriber['lang'] = $abonne['lang'];
		}
		$subscribe($email, $data_subscriber);
		$i++;
		if (time() >= $max_time) {
			spip_log("mailsubscribers_synchronise_liste $liste: $i/".count($abonnes_emails)." abonnes mais temps ecoule", 'mailsubscribers' . _LOG_DEBUG);
			return false;
		}
	}

	// baisser les drapeaux edition de tout ce qu'on vient de faire
	if (function_exists('debloquer_tous')) {
		$id_a = (isset($GLOBALS['visiteur_session']['id_auteur']) ? $GLOBALS['visiteur_session']['id_auteur'] : $GLOBALS['ip']);
		debloquer_tous($id_a);
	}

	return true;
}
