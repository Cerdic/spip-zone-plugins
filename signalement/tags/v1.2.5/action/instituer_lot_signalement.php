<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Action d'édition par lot de signalements
 *
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_lot_signalement_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// verifier les droits
	if (autoriser('instituer','signalement',0)){

		/**
		 * Cas 1 : les arguments sont explicites
		 * statut-ip/email/id_auteur/auteur
		 *
		 */
		if (preg_match(",^(\w+)-,",$arg,$match)
		 AND in_array($statut=$match[1],array('publie','off','spam'))){
			$arg = substr($arg,strlen($statut)+1);

			$arg = explode('/',$arg);
			$ip = array_shift($arg);
			$email_auteur = array_shift($arg);
			$id_auteur = intval(array_shift($arg));
			$auteur = implode('/',$arg);
			$where = array();
			// pas de moderation par lot sur les signalement prives
			$where[] = sql_in('statut',array('privadm','prive','privrac'),'NOT');
			if ($ip) $where[] = "ip=".sql_quote($ip);
			if ($email_auteur) $where[] = "email_auteur=".sql_quote($email_auteur);
			if ($id_auteur) $where[] = "id_auteur=".intval($id_auteur);
			if ($auteur) $where[] = "auteur=".sql_quote($auteur);
			$rows = sql_allfetsel("*", "spip_signalements", $where);
			if (!count($rows)) return;

			include_spip('action/instituer_signalement');
			foreach ($rows as $row) {
				instituer_un_signalement($statut,$row);
			}
		}
		/**
		 * Cas 2 : seul le statut est explicite et signe
		 * les id concernes sont passes en arg supplementaires
		 * dans un taleau ids[]
		 */
		elseif (preg_match(",^(\w+)$,",$arg,$match)
		 AND in_array($statut=$match[1],array('publie','off','spam'))
		 AND $id=_request('ids')
		 AND is_array($id)){

			$ids = array_map('intval',$id);
			$where = array();
			// pas de moderation par lot sur les signalement prives
			$where[] = sql_in('id_signalement',$ids);
			$rows = sql_allfetsel("*", "spip_signalements", $where);
			if (!count($rows)) return;

			include_spip('action/instituer_signalement');
			foreach ($rows as $row) {
				instituer_un_signalement($statut,$row);
			}
		}
	}
	else {
		spip_log("instituer_lot_signalement interdit pour auteur ".$GLOBALS['visiteur_session']['id_auteur'],_LOG_ERREUR);
	}

}

?>
