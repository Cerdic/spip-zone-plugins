<?php
/**
 * Plugin No-SPAM
 * (c) 2008-2011 Cedric Morin Yterium.net
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Lister les formulaires a prendre en charge contre le SPAM
 * pour verifier le nobot et le jeton sur un formulaire, l'ajouter a cette liste
 * par le pipeline nospam_lister_formulaires
 * @return void
 */
function nospam_lister_formulaires() {
	if (!isset($GLOBALS['formulaires_no_spam']))
		$GLOBALS['formulaires_no_spam'] = array();
	$formulaires = array_merge($GLOBALS['formulaires_no_spam'], array('forum', 'ecrire_auteur', 'signature'));
	return pipeline('nospam_lister_formulaires', $formulaires);
}

/**
 * Ajouter le champ de formulaire 'nobot' au besoin
 *
 * @param array $flux
 * @return array
 */
function nospam_recuperer_fond($flux) {
	// determiner le nom du formulaire
	$fond = strval($flux['args']['fond']);
	if (false !== $pos = strpos($fond, 'formulaires/')) {
		$form = substr($fond, $pos + 12);
		if (in_array($form, nospam_lister_formulaires())) {
			// on ajoute le champ 'nobot' si pas present dans le formulaire
			$texte = &$flux['data']['texte'];
			if ((false === strpos($texte, 'name="nobot"'))
				and (false !== $pos = strpos($texte, '</form>'))
			) {
				$nobot = recuperer_fond("inclure/nobot", array('nobot' => ''));
				$texte = substr_replace($texte, $nobot, $pos, 0);
			}
		}
	}
	return $flux;
}

/**
 * Ajouter un jeton temporaire lie a l'heure et a l'IP pour limiter la reutilisation possible du formulaire
 *
 * @param array $flux
 * @return array
 */
function nospam_formulaire_charger($flux) {
	$form = $flux['args']['form'];
	if (in_array($form, nospam_lister_formulaires())
		AND $flux['data']
			AND is_array($flux['data'])
	) {
		include_spip("inc/nospam");
		$jeton = creer_jeton($form);
		$flux['data']['_hidden'] .= "<input type='hidden' name='_jeton' value='$jeton' />";
	}
	return $flux;
}

/**
 * Verifier le jeton temporaire lie a l'heure et a l'IP pour limiter la reutilisation possible du formulaire
 *
 * @param array $flux
 * @return array
 */
function nospam_formulaire_verifier($flux) {
	$form = $flux['args']['form'];
	if (in_array($form, nospam_lister_formulaires())) {
		include_spip("inc/nospam");
		$jeton = _request('_jeton');
		// le jeton prend en compte l'heure et l'ip de l'internaute
		if (_request('nobot') // trop facile !
			OR (!verifier_jeton($jeton, $form))
		) {
			#spip_log('pas de jeton pour '.var_export($flux,true),'nospam');
			$flux['data']['message_erreur'] .= _T('nospam:erreur_jeton');
			if ($form == 'forum')
				unset($flux['data']['previsu']);
		}

		// pas la peine de filtrer les contenus postés par un admin
		if (!isset($GLOBALS['visiteur_session']['statut']) OR $GLOBALS['visiteur_session']['statut'] != '0minirezo') {
			if ($verifier_formulaire = charger_fonction("verifier_formulaire_$form", "nospam", true)) {
				$flux = $verifier_formulaire($flux);
				// recuperation de la liste des IPs blacklist/greylist
				// async si on renvoie la previsu ou si erreur
				$async = false;
				if (count($flux['data']))
					$async = true;
				nospam_update_ip_list($async);
			}
		}
	}
	return $flux;
}

/**
 * Au moment de decider du statut d'un forum,
 * quelques verifications et une moderation si necessaire !
 *
 * @param array $flux
 * @return array
 */
function nospam_pre_edition($flux) {
	if ($flux['args']['table'] == 'spip_forum'
		AND $flux['args']['action'] == 'instituer'
	) {

		// ne pas publier automatiquement certains messages suspects ...
		// sauf si le posteur a de toute facon le pouvoir de moderer et de se publier
		include_spip('inc/autoriser');
		if ($flux['data']['statut'] == 'publie'
			AND (!isset($GLOBALS['visiteur_session']['statut']) OR !autoriser('modererforum'))
		) {
			// verifier le status de cette IP
			nospam_check_ip_status($GLOBALS['ip']);

			$email = strlen($flux['data']['email_auteur']) ? " OR email_auteur=" . sql_quote($flux['data']['email_auteur']) : "";
			$spammeur_connu = (!isset($GLOBALS['visiteur_session']['statut'])
				AND (
					isset($GLOBALS['ip_greylist'][$GLOBALS['ip']])
					OR isset($GLOBALS['ip_blacklist'][$GLOBALS['ip']])
					OR sql_countsel('spip_forum', '(ip=' . sql_quote($GLOBALS['ip']) . "$email) AND statut='spam'") > 0
				)
			);

		  // activer aussi le flag spammeur connu en cas de flood, meme si aucune detection spam jusqu'ici
		  // on sera plus severe sur les liens dans ce cas
		  // cas du spammeur qui envoie que des messages a 3 liens a haute frequence (passe a travers tous les filtres)
		  // au bout du 5e message en <10min ou 10e en <30min on va moderer tout message avec un lien
		  if (!$spammeur_connu){
			  if (($nb=sql_countsel('spip_forum','(ip='.sql_quote($GLOBALS['ip']).$email.') AND '.sql_date_proche('date_heure','-30','minute')))>=7){
			  spip_log("[Flood] $nb message pour (ip=".$GLOBALS['ip']."$email) dans les 30 dernieres minutes",'nospam');
			  $spammeur_connu = true;
			  }
		  }
		  if (!$spammeur_connu){
			  if (($nb=sql_countsel('spip_forum','(ip='.sql_quote($GLOBALS['ip']).$email.') AND '.sql_date_proche('date_heure','-10','minute')))>=3){
			  spip_log("[Flood] $nb message pour (ip=".$GLOBALS['ip']."$email) dans les 10 dernieres minutes",'nospam');
			  $spammeur_connu = true;
			  }
		  }

			$lang_suspecte = false;
			// si c'est un spammeur connu,
			// verifier que cette ip n'en est pas a son N-ieme spam en peu de temps
			// a partir d'un moment on refuse carrement le spam massif, le posteur devra attendre pour reposter
			if ($spammeur_connu) {
				// plus de 30 spams dans les dernieres 2h, faut se calmer ...
				// ou plus de 10 spams dans la dernieres 1h, faut se calmer ...
				if (
					($nb = sql_countsel('spip_forum', 'statut=\'spam\' AND (ip=' . sql_quote($GLOBALS['ip']) . $email . ') AND ' . sql_date_proche('date_heure','-120','minute'))) >= 30
					OR
					($nb = sql_countsel('spip_forum', 'statut=\'spam\' AND (ip=' . sql_quote($GLOBALS['ip']) . $email .') AND ' . sql_date_proche('date_heure','-60','minute'))) >= 10
					){
					$flux['data']['statut'] = ''; // on n'en veut pas !
					spip_log("[Refuse] $nb spam pour (ip=" . $GLOBALS['ip'] . "$email) dans les 2 dernieres heures", 'nospam');
					return $flux;
				}
			}
			// sinon regarder si l'objet a une langue, et si le post est dans la meme langue ou non
			// en cas de langue differente, on se mefie
			elseif ($flux['data']['objet']){
				$table = table_objet_sql($flux['data']['objet']);
				$trouver_table = charger_fonction("trouver_table","base");
				if ($desc = $trouver_table($table)
				  AND isset($desc['field']['lang'])){
					$primary = id_table_objet($flux['data']['objet']);
					$lang_objet = sql_getfetsel("lang",$table,"$primary=".intval($flux['data']['id_objet']));
					include_spip("inc/detecter_langue");
					$lang_post = _detecter_langue($flux['data']['texte']);
					if ($lang_post!==$lang_objet)
						$lang_suspecte = true;
				}
			}

			// si c'est un message bourre de liens, on le modere
			// le seuil varie selon le champ et le fait que le spammeur est deja connu ou non
			$seuils = array(
				// seuils par defaut
				0 => array(
					0 => array(1 => 'prop', 3 => 'spam'), // seuils par defaut
					'url_site' => array(2 => 'spam'), // 2 liens dans le champ url, c'est vraiment louche
					'texte' => array(4 => 'prop', 20 => 'spam') // pour le champ texte
				),
				// seuils severises pour les suspects : modere en prop des qu'il y a un lien, spam si plus de 5
				'suspect' => array(
					0 => array(1 => 'spam'),
					'url_site' => array(2 => 'spam'), // 2 liens dans le champ url, c'est vraiment louche
					'texte' => array(1 => 'prop', 5 => 'spam')
				),
				// seuils pour les blacklist : si pas de lien on passe en prop par precaution, sinon en spam
				'blacklist' => array(
					0 => array(1 => 'spam'),
					'url_site' => array(2 => 'spam'), // 2 liens dans le champ url, c'est vraiment louche
					'texte' => array(0 => 'prop', 1 => 'spam')
				)
			);

			$seuils = isset($GLOBALS['ip_blacklist'][$GLOBALS['ip']])? $seuils['blacklist'] : (($spammeur_connu OR $lang_suspecte) ? $seuils['suspect'] : $seuils[0]);
			include_spip("inc/nospam"); // pour analyser_spams()
			foreach ($flux['data'] as $champ => $valeur) {
				$infos = analyser_spams($valeur);
				if ($infos['contenu_cache']) {
					// s'il y a du contenu caché avec des styles => spam direct
					$flux['data']['statut'] = 'spam';
					spip_log("\t" . $flux['data']['auteur'] . "\t" . $GLOBALS['ip'] . "\t" . "requalifié en spam car contenu cache", 'nospam');
				}
				elseif ($infos['nombre_liens'] > 0) {
					// si un lien a un titre de moins de 3 caracteres, c'est louche...
					if ($infos['caracteres_texte_lien_min'] < 3) {
						$flux['data']['statut'] = 'prop'; // en dur en attendant une idee plus generique
						spip_log("\t" . $flux['data']['auteur'] . "\t" . $GLOBALS['ip'] . "\t" . "requalifié en prop car moins de 3car hors liens", 'nospam');
					}

					if (isset($seuils[$champ]))
						$seuil = $seuils[$champ];
					else
						$seuil = $seuils[0];

					foreach ($seuil as $s => $stat)
						if ($infos['nombre_liens'] >= $s) {
							$flux['data']['statut'] = $stat;
							spip_log("\t" . $flux['data']['auteur'] . "\t" . $GLOBALS['ip'] . "\t" . "requalifié en " . $stat . " car nombre_liens >= " . $s, 'nospam');
						}

					if ($flux['data']['statut'] != 'spam') {
						$champs = array_unique(array('texte', $champ));
						$nb_tolerance = 3;
						if ($h = rechercher_presence_liens_spammes($infos['liens'], $nb_tolerance, 'spip_forum', $champs)) {
							$flux['data']['statut'] = 'spam';
							spip_log("\t" . $flux['data']['auteur'] . "\t" . $GLOBALS['ip'] . "\t" . "requalifié en spam car lien $h deja dans un spam", 'nospam');
						}
					}
				}
			}


			// verifier qu'un message identique n'a pas ete publie il y a peu
			if ($flux['data']['statut'] != 'spam') {
				if (sql_countsel('spip_forum', 'texte=' . sql_quote($flux['data']['texte']) . " AND statut IN ('publie','off','spam')") > 0){
					$flux['data']['statut'] = 'spam';
					spip_log("\t" . $flux['data']['auteur'] . "\t" . $GLOBALS['ip'] . "\t" . "requalifié en spam car message identique deja existant", 'nospam');
				}
			}
			// verifier que cette ip n'en est pas a son N-ieme post en peu de temps
			// plus de 5 messages en 5 minutes c'est suspect ...
			if ($flux['data']['statut'] != 'spam') {
				if (($nb = sql_countsel('spip_forum', 'ip=' . sql_quote($GLOBALS['ip']) . ' AND ' . sql_date_proche('date_heure','-5','minute'))) >= 5){
					$flux['data']['statut'] = 'spam';
					spip_log("[Flood2] $nb message pour (ip=".$GLOBALS['ip']."$email) dans les 5 dernieres minutes : requalif en spam",'nospam');
				}
			}
		}
	}
	return $flux;
}



/**
 * Fermer la connexion pour que le visiteur n'attende pas apres le curl sur nospam.spip.net
 * @param $content
 * @return mixed
 */
function nospam_flush_close($content){
	header("Content-Length: ".($l=ob_get_length()));
	header("Connection: close");
	return $content;
}

/**
 * Flusher et lancer l'update de la liste des ip
 */
function nospam_flush_and_update(){
	chdir(_ROOT_CWD); // securite en cas de register_shutdown_function
	// forcer le flush des tampons pas envoyes (declenche le content-length/conection:close envoye dans cache_cool_flush)
	$flush_level = ob_get_level();
	while ($flush_level--) ob_end_flush();
	flush();
	if (function_exists('fastcgi_finish_request'))
		fastcgi_finish_request();
	nospam_update_ip_list();
}

if (!defined('_NOSPAM_IP_LIST_CACHE')) define('_NOSPAM_IP_LIST_CACHE',10800);
/**
 * Recuperer la liste des IP black ou grey sur nospam.spip.net
 * si on a pas une liste a jour
 * et la stocker dans un fichier
 * @param bool $async
 */
function nospam_update_ip_list($async=false){
	$file = _DIR_TMP."nospam_ip_list.txt";
	if (file_exists($file) AND filemtime($file)>time()-_NOSPAM_IP_LIST_CACHE)
		return;
	spip_log("nospam_update_ip_list:$async","nospam");

	if ($async){
		// indiquer de fermer la connexion dans la foulee
		// pour faire le hit de recuperation async hors temps d'attente
		ob_start("nospam_flush_close");
		register_shutdown_function("nospam_flush_and_update");
		return;
	}

	// on fait d'abord un touch car si le recuperer_page echoue (hebergeurs qui interdisent)
	// on ne veut pas recommencer plein de fois de suite
	@touch($file);
	$url_api = "http://nospam.spip.net/spamsignal.api/list";
	include_spip("inc/distant");
	include_spip("inc/json");
	$res = recuperer_page($url_api);
	if ($res
	  AND function_exists("json_decode")
	  AND $liste = json_decode($res,true)){
		ecrire_fichier($file,serialize($liste));
	}
}

/**
 * Verifier le status d'une IP et la noter dans la globale ip_blacklist ou ip_greylist si c'est une IP louche
 * @param $ip
 * @return string
 *   ok|grey|black
 */
function nospam_check_ip_status($ip){
	$file = _DIR_TMP."nospam_ip_list.txt";
	if (!file_exists($file) OR filemtime($file)<time()-_NOSPAM_IP_LIST_CACHE)
		return;

	lire_fichier($file,$liste);
	spip_log("nospam_check_ip_status:$ip","nospam");
	if ($liste = unserialize($liste)){
		#spip_log($liste,"nospam");
		$now = date('Y-m-d H:i:s');
		$ip_family = preg_replace(",([.:])[^.:]$,","$1*",$ip);
		spip_log("ip $ip famille $ip_family","nospam");
		foreach(array("blacklist","greylist") AS $l){
			if (isset($liste[$l][$ip])
				AND $liste[$l][$ip]>$now){
				$GLOBALS['ip_'.$l][$ip] = true;
				spip_log("$ip ajoute a ip_$l","nospam");
				return ($l=="blacklist"?"black":"grey");
			}
			if (isset($liste[$l][$ip_family])
				AND $liste[$l][$ip_family]>$now){
				$GLOBALS['ip_'.$l][$ip] = true;
				spip_log("$ip ajoute a ip_$l (famille $ip_family)","nospam");
				return ($l=="blacklist"?"black":"grey");
			}
		}
	}
	return "ok";
}
?>