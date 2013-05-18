<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function notifavancees_notifications($flux){
	// On récupère les infos
	$quoi = $flux['args']['quoi'];
	$id = intval($flux['args']['id']);
	$options = $flux['args']['options'];
	
	// On cherche d'abord les destinataires vu que s'il n'y en a pas, on ne fait rien :)
	$destinataires = notifications_destinataires($quoi, $id, $options);
	
	// Youpiiii la liste des destinataires est terminée
	// On ne continue que s'il en reste
	if ($destinataires){
		include_spip('inc/filtres');
		// On récupère les abonnés explicites pour connaître les éventuelles préférences d'envoi
		$preferences = notifications_abonnes($quoi, $id);
		
		// On programme les envois pour chaque destinataire un par un
		foreach ($destinataires as $cle=>$destinataire){
			// Si c'est un tableau avec déjà toutes les infos
			if (is_array($destinataire)){
				foreach ($destinataire as $mode=>$contact){
					job_queue_add('notifications_envoyer', "Notification ($quoi, $id) par le mode $mode pour <$contact>", array($contact, $mode, $quoi, $id, $options), 'notifavancees_pipelines');
				}
			}
			// Sinon c'est soit un id_auteur soit un mail
			// Si on le trouve dans les préférences, on les suit
			elseif ($modes = $preferences[$destinataire]['modes']){
				foreach ($modes as $mode){
					job_queue_add('notifications_envoyer', "Notification ($quoi, $id) par le mode $mode pour <$destinataire>", array($destinataire, $mode, $quoi, $id, $options), 'notifavancees_pipelines');
				}
			}
			// Si c'est pas dans les préférences
			// et que $destinataire est SOIT un mail SOIT un id_auteur
			// (on vérifie quand même et on ne traite pas les autres cas qui à priori sont pathologiques)
			// alors on envoie uniquement par courriel par défaut
			elseif ((intval($destinataire) == $destinataire and $destinataire > 0) or email_valide($destinataire)){
				$mode = 'email';
				job_queue_add('notifications_envoyer', "Notification ($quoi, $id) par le mode $mode pour <$destinataire>", array($destinataire, $mode, $quoi, $id, $options), 'notifavancees_pipelines');
			}
		}
	}
}

/*
 * Retourne la liste des destinataires pour une notification précise.
 * 
 * La liste est un tableau pouvant être composé de trois choses :
 * - Un identifiant d'auteur
 * - Une adresse de courriel
 * - Un tableau associatif mode=>information définissant directement les modes de contact, par exemple 'email'=>'truc@machin.com'
 *
 * Les destinataires peuvent provenir de trois sources différentes :
 * - La notification "truc" peut définir une fonction notifications_truc_destinataires()
 * - La table "spip_notifications_abonnements" qui défini les abonnés explicites
 * - Le pipeline "notifications_destinataires"
 *
 * @param string $quoi Le nom de la notification
 * @param int $id Un éventuel identifiant d'objet lié à la notification
 * @param array $options Des options supplémentaires
 * @return array Retourne un tableau de destinataires sous la forme décrite ci-dessus
 */
function notifications_destinataires($quoi, $id=0, $options=array()){
	// On retourne toujours un tableau
	$destinataires = array();
	
	// En premier les destinataires choisis par la notification
	if ($fonction_destinataires = charger_fonction('destinataires', "notifications/$quoi", true))
		$destinataires = $fonction_destinataires($id, $options);
	
	// Ensuite, ceux qui sont abonnés explicitement
	// Pour les préférences, la notification peut définir une fonction notifications_truc_preferences()
	$abonnes = notifications_abonnes($quoi, $id);
	if (is_array($abonnes) and $abonnes){
		// On cherche l'éventuelle fonction qui sait gérer les préférences
		$fonction_preferences = charger_fonction('preferences', "notifications/$quoi", true);
		// On teste tous les abonnés un par un
		foreach ($abonnes as $dest=>$infos){
			// S'il n'y pas de préférences, on ajoute directement
			if (!$infos['preferences']){
				$destinataires[] = $dest;
			}
			// Sinon on applique le test de la fonction dédiée pour savoir si on ajoute
			elseif ($fonction_preferences and $fonction_preferences($id, $options, $infos['preferences'])){
				$destinataires[] = $dest;
			}
		}
	}
	
	// Ensuite on passe dans le pipeline
	$destinataires = pipeline(
		'notifications_destinataires',
		array(
			'args' => array('quoi'=>$quoi, 'id'=>$id, 'options'=>$options),
			'data' => $destinataires
		)
	);
	
	// On supprime les doublons
	if (is_array($destinataires))
		$destinataires = array_unique($destinataires);
	
	// Enfin on retire ceux qui se sont blacklistés explicitement
	if ($blacklist = notifications_abonnes($quoi, $id, true))
		$destinataires = notifications_exclure_destinataires($destinataires, $blacklist);
	
	return $destinataires;
}

/*
 * Exclure une liste de destinataires d'une autre liste
 *
 * @param array $destinataires La liste initiale de destinataires
 * @param array @blacklist La liste qu'on veut exclure
 * @return array Retourne la première liste moins la seconde
 */
function notifications_exclure_destinataires($destinataires, $blacklist){
	foreach ($blacklist as $exclu){
		// Si on le trouve direct, soit l'auteur, soit le contact, on le vire
		if ($cles = array_keys($destinataires, $exclu)){
			foreach ($cles as $cle)
				unset($destinataires[$cle]);
		}
		// Sinon on essaye de le trouver dans les tableaux éventuels
		else{
			$destinataires_tableau = array_filter($destinataires, 'is_array');
			foreach ($destinataires_tableau as $cle => $tableau){
				if (in_array($exclu, $tableau))
					unset($destinataires[$cle]);
			}
		}
	}
	
	return $destinataires;
}

/*
 * Retourne la liste des abonnés explicites à une notification.
 * Cette fonction est à utiliser pour ne pas refaire plusieurs appels à la base de données dans un même hit PHP.
 *
 * Si on demande les abonnés le tableau est de la forme id=>modes.
 * Si on demande les blacklistés le tableau contient la liste directement, soit l'id_auteur soit le contact.
 *
 * @param string $quoi Le nom de la notification
 * @param int $id Un éventuel identifiant d'objet lié à la notification
 * @param bool $blacklist Indique si l'on retourne ceux qui ne veulent PAS être notifiés
 * @return array Retourne un tableau des abonnés ou des blacklistés
 */
function notifications_abonnes($quoi, $id=0, $blacklist=false){
	static $abonnes = array();
	static $blacklistes = array();
	
	// On normalise l'id
	if (!($id = intval($id)) or !($id > 0))
		$id = 0;
	
	// On ne fait la requête que si on a pas déjà les valeurs
	if (
		(!$blacklist and !isset($abonnes[$quoi][$id]))
		or ($blacklist and !isset($blacklistes[$quoi][$id]))
	){
		include_spip('base/abtract_sql');
		
		$where = array(
			'quoi = '.sql_quote($quoi)
		);
	
		// S'il y a un id pertinent on le rajoute à la requête
		if ($id > 0)
			$where[] = 'id = '.$id;
		
		// On va chercher tous les gens liés à cette notification
		$requete = sql_allfetsel(
			'id_auteur, contact, preferences, modes, actif',
			'spip_notifications_abonnements',
			$where
		);
		
		$abonnes[$quoi][$id] = $blacklistes[$quoi][$id] = array();
		foreach ($requete as $ligne){
			// On ne fait quelque chose que si l'abonnement est actif !
			if ($ligne['actif']){
				// S'il y a des préférences de modes d'envoi, c'est un abonné
				if ($modes = trim($ligne['modes']) and $modes = unserialize($modes) and is_array($modes)){
					$infos = array('modes'=>$modes, 'preferences'=>unserialize($ligne['preferences']));
					// Si c'est un auteur on met ça comme clé
					if ($ligne['id_auteur'] > 0)
						$abonnes[$quoi][$id][$ligne['id_auteur']] = $infos;
					// Sinon on met l'information de contact
					else
						$abonnes[$quoi][$id][$ligne['contact']] = $infos;
				}
				// Sinon c'est un blacklisté
				else{
					// Si c'est un auteur on met l'id
					if ($ligne['id_auteur'] > 0)
						$blacklistes[$quoi][$id][] = $ligne['id_auteur'];
					// Sinon on met l'information de contact
					else
						$blacklistes[$quoi][$id][] = $ligne['contact'];
				}
			}
		}
	}
	
	// On retourne
	if (!$blacklist)
		return $abonnes[$quoi][$id];
	else
		return $blacklist[$quoi][$id];
}

/*
 * Liste toutes les notifications installées
 *
 * @return array Un tableau listant les notifications et leurs informations
 */
function notifications_lister_disponibles(){
	static $notifications = null;
	
	if (is_null($notifications)){
		$notifications = array();
		$liste = find_all_in_path('notifications/', '.+[.]yaml$');
		
		if (count($liste)){
			foreach ($liste as $fichier=>$chemin){
				$type_notification = preg_replace(',[.]yaml$,i', '', $fichier);
				$dossier = str_replace($fichier, '', $chemin);
				if (is_array($notification = notifications_charger_infos($type_notification))){
					$notifications[$type_notification] = $notification;
				}
			}
		}
	}
	
	return $notifications;
}

/*
 * Charger les informations contenues dans le yaml d'une notification
 *
 * @param string $type_notification Le type de la notification
 * @return array Un tableau contenant le YAML décodé
 */
function notifications_charger_infos($type_notification){
	include_spip('inc/yaml');
	$fichier = find_in_path("notifications/$type_notification.yaml");
	$notification = yaml_decode_file($fichier);
	if (is_array($notification)){
		$notification['titre'] = $notification['titre'] ? _T_ou_typo($notification['titre']) : $type_notification;
		$notification['description'] = $notification['description'] ? _T_ou_typo($notification['description']) : '';
		$notification['icone'] = $notification['icone'] ? find_in_path($notification['icone']) : '';
	}
	return $notification;
}

/*
 * Liste tous les modes d'envoi installés.
 *
 * @return array Un tableau listant les modes et leurs informations
 */
function notifications_modes_lister_disponibles(){
	static $modes = null;
	
	if (is_null($modes)){
		$modes = array();
		$liste = find_all_in_path('notifications/modes/', '.+[.]yaml$');
		
		if (count($liste)){
			foreach ($liste as $fichier=>$chemin){
				$type_mode = preg_replace(',[.]yaml$,i', '', $fichier);
				$dossier = str_replace($fichier, '', $chemin);
				// On ne garde que les modes qui ont bien la fonction d'envoi
				if (charger_fonction('envoyer', "notifications/modes/$type_mode/", true)
					and (
						is_array($mode = notifications_modes_charger_infos($type_mode))
					)
				){
					$modes[$type_mode] = $mode;
				}
			}
		}
	}
	
	return $modes;
}

/*
 * Charger les informations contenues dans le yaml d'un mode d'envoi
 *
 * @param string $type_mode Le type du mode d'envoi
 * @return array Un tableau contenant le YAML décodé
 */
function notifications_modes_charger_infos($type_mode){
	include_spip('inc/yaml');
	$fichier = find_in_path("notifications/modes/$type_mode.yaml");
	$mode = yaml_decode_file($fichier);
	if (is_array($mode)){
		$mode['titre'] = $mode['titre'] ? _T_ou_typo($mode['titre']) : $type_mode;
		$mode['description'] = $mode['description'] ? _T_ou_typo($mode['description']) : '';
		$mode['choix'] = $mode['choix'] ? _T_ou_typo($mode['choix']) : '';
		$mode['icone'] = $mode['icone'] ? find_in_path($mode['icone']) : '';
	}
	return $mode;
}

/*
 * Fonction centrale d'envoi d'UNE notification.
 * C'est elle qui fait la jonction entre un destinataire, un mode d'envoi, et le bon contenu approprié au mode.
 *
 * @param mixed $contact Le destinataire à qui envoyer, cela peut-être un id_auteur, ou une information de contact (mail, téléphone, etc)
 * @param string $mode Le mode d'envoi à utiliser
 * @param string $quoi La nom de la notification
 * @param int $id Un éventuel identifiant d'objet lié à la notification
 * @param array $options Des options supplémentaires
 * @return bool Retourne false si une erreur se produit
 */
function notifications_envoyer($destinataire, $mode, $quoi, $id=0, $options=array()){
	// On commence par aller chercher la bonne information de contact adapté au mode
	// Car si on ne la trouve pas... on envoie rien
	if (
		$mode_envoyer = charger_fonction('envoyer', "notifications/modes/$mode/", true)
		and $mode_contact = charger_fonction('contact', "notifications/modes/$mode/", true)
		and $contact = $mode_contact($destinataire)
	){
		// On cherche maintenant le contenu
		$contenu = array();
		// Si la notification a une fonction dédiée au contenu, c'est ça qu'on prend
		if ($notification_contenu = charger_fonction('contenu', "notifications/$quoi/", true)){
			$contenu_tmp = $notification_contenu($id, $options, $destinataire, $mode);
			if (is_array($contenu_tmp))
				$contenu = $contenu_tmp;
			elseif (is_string($contenu_tmp))
				$contenu['texte'] = $contenu_tmp;
		}
		
		// On construit le contexte utile
		$contexte = array(
			'quoi' => $quoi,
			'id' => $id,
			'options' => $options,
			'destinataire' => $destinataire,
			'contact' => $contact,
			'mode' => $mode
		);
		
		// Pour ajouter des informations utiles on cherche un objet dans le nom de la notif
		include_spip('base/abstract_sql');
		include_spip('base/objets');
		$type_objet = explode('_', $quoi);
		array_pop($type_objet); // on enlève toujours le dernier mot
		// Si on est en SPIP 3, on fait une meilleure recherche
		if (function_exists('lister_tables_objets_sql')) {
			while (!empty($type_objet)) {
				// Si le nom qui reste fait partie des objets éditoriaux on s'arrête
				if (in_array(
					table_objet_sql(join('_', $type_objet)),
					array_keys(lister_tables_objets_sql())
				)) {
					$type_objet = join('_', $type_objet);
					break;
				}
				// Sinon on raccourcit du dernier élément et on continue de chercher
				else {
					array_pop($type_objet);
				}
			}
		}
		// Sinon en SPIP 2, le dernier mot séparé par "_" est considéré comme le vrai nom de notif,
		// et on garde le reste comme étant un objet
		elseif (!empty($type_objet)) {
			$type_objet = join('_', $type_objet);
		}
		// Sinon pas d'objet du tout
		else {
			$type_objet = false;
		}
		// On ajoute au contexte si trouvé
		if ($type_objet) {
			$cle_objet = id_table_objet($type_objet);
			$contexte['objet'] = $type_objet;
			$contexte['id_objet'] = $id;
			$contexte[$cle_objet] = $id;
		}

		//Si un expéditeur est défini on l'utilise
		if ($options['from'])
			$contenu['from'] = $options['from'];
		
		//si un nom d'expéditeur est défini
		if ($options['nom_envoyeur'])
			$contenu['nom_envoyeur'] = $options['nom_envoyeur'];
		
		// Le contenu de base est le contenu texte
		// S'il n'existe pas on cherche le squelette directement
		if (!$contenu['texte'] and find_in_path("notifications/${quoi}.html")){
			$contenu['texte'] = trim(recuperer_fond(
				"notifications/$quoi",
				$contexte
			));
		}
		
		// On ne continue que si on a bien un texte de base
		if ($contenu['texte']){
			// Existe-t-il une version HTML ? Sinon le squelette
			if (!$contenu['html'] and find_in_path("notifications/${quoi}_html.html")){
				$contenu['html'] = trim(recuperer_fond(
					"notifications/${quoi}_html",
					$contexte
				));
			}
			
			// Existe-t-il une version courte ?
			if (!$contenu['court']){
				// Sinon le squelette
				if (find_in_path("notifications/${quoi}_court.html")){
					$contenu['court'] = trim(recuperer_fond(
						"notifications/${quoi}_court",
						$contexte
					));
				}
				// Sinon on la construit à partir de la première ligne
				else{
					include_spip('inc/texte');
					// Nettoyer un peu les retours chariots
					$contenu['court'] = str_replace("\r\n", "\r", $contenu['texte']);
					$contenu['court'] = str_replace("\r", "\n", $contenu['court']);
					// Découper
					$contenu['court'] = explode("\n",trim($contenu['court']));
					// Extraire la premiere ligne
					$contenu['court'] = array_shift($contenu['court']);
					// La couper si besoin
					$contenu['court'] = couper($contenu['court'], 130);
				}
			}
			
			// Maintenant qu'on a tout on appelle le mode d'envoi
			return $mode_envoyer($contact, $contenu);
		}
	}
	
	return false;
}

function notifavancees_affiche_droite($flux){
	if (in_array($flux['args']['exec'], array('auteur_infos', 'infos_perso'))){
		$boite = recuperer_fond(
			'prive/boite/notifications_auteur',
			array(
				'id_auteur' => $flux['args']['id_auteur']
			)
		);
		$flux['data'] .= $boite;
	}
	
	return $flux;
}

?>
