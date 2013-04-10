<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Pipelines utilisés par Signalement
 *
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;
 
/**
 * Insertion dans le pipeline afficher_config_objet (SPIP)
 * 
 * Boite de configuration des objets
 * Ajout du nombre de signalement sur les objets
 *
 * @param array $flux Le contexte du pipeline
 * @return array $flux Le contexte du pipeline
 */
function signalement_afficher_config_objet($flux){
	if (($type = $flux['args']['type'])
		AND $id = $flux['args']['id']){
		if (autoriser('moderersignalement', $type, $id)) {
			$id_table_objet = id_table_objet($type);
			$flux['data'] .= recuperer_fond("prive/objets/configurer/signalement",array('id_objet'=>$id,'objet'=>  objet_type(table_objet($type))));
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline notifications_destinataires (SPIP)
 * 
 * Sur les notifications de signalements, on ajoute des destinataires
 * 
 * @param array $flux le tableau des destinataires
 * @return array $flux le tableau complété
 */
function signalement_notifications_destinataires($flux){
	if($flux['args']['quoi'] == 'instituersignalement'){
		include_spip('inc/config');
		if(is_array(lire_config('signalement/notif_publication')) AND count(lire_config('signalement/notif_publication')) > 0){
			foreach(lire_config('signalement/notif_publication') as $id_auteur){
				$flux['data'][] = sql_getfetsel('email','spip_auteurs','id_auteur='.intval($id_auteur));
			}
		}else{
			$admins = sql_select('email','spip_auteurs','statut='.sql_quote('0minirezo'));
			while($admin = sql_fetch($admins)){
				$flux['data'][] = $admin['email'];
			}
		}
	}
	return $flux;	
}

/**
 * Insertion dans le pipeline post_edition (SPIP)
 * 
 * Lors de l'institution d'un signalement, on vérifie si les options de dépublication et de seuil
 * sont présentes.
 * Lors de la publication d'un signalement, si on dépasse le seuil de signalements publiés pour un objet, 
 * on le dépublie s'il est publié
 * Lors du refus d'un signalement, si on descend en dessous du seuil de signalements publiés pour un objet et
 * que le statut de cet objet est refusé, on le remet en publié.
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux Le contexte du pipeline  
 */
function signalement_post_edition($flux){
	if(($flux['args']['table'] == 'spip_signalements')
		AND ($flux['args']['action'] == 'instituer')
	){
		include_spip('inc/config');
		/**
		 * On ne continue que si les options de dépublication et de seuil sont présentes
		 */
		if(lire_config('signalement/depublier','') == 'on'){
			$seuil = intval(lire_config('signalement/depublier_seuil','5'));
			if($seuil > 0){
				$signalement = sql_fetsel('*','spip_signalements','id_signalement='.intval($flux['args']['id_objet']));
				$objet_signale = $signalement['objet'];
				$id_objet_signale = $signalement['id_objet'];
				$compte_signalement_objet = sql_countsel('spip_signalements','id_objet='.intval($id_objet_signale).' AND objet='.sql_quote($objet_signale).' AND statut="publie"');
				/**
				 * Cas où l'objet est publié et que le compte de signalement est supérieur au seuil,
				 * on dépublie
				 */
				if($compte_signalement_objet >= $seuil){
					$table_objet_signale = table_objet_sql($objet_signale);
					$id_table_objet_signale = id_table_objet($objet_signale);
					$id_objet_signale = sql_getfetsel("$id_table_objet_signale","$table_objet_signale","$id_table_objet_signale=".intval($id_objet_signale));
					if($id_objet_signale){
						include_spip('action/editer_objet');
						$visiteur = $GLOBALS['visiteur_session'];
						$GLOBALS['visiteur_session'] = sql_fetsel('*','spip_auteurs','statut="0minirezo" AND webmestre="oui"');
						objet_instituer($objet_signale,$id_objet_signale,array('statut'=>'refuse'));
						$GLOBALS['visiteur_session'] = $visiteur;
						// Notifications
						if ($notifications = charger_fonction('notifications', 'inc')) {
							$notifications("depubliersignalement", $id);
						}
					}
				}
				/**
				 * Cas où l'objet est refusé et que le compte de signalement est inférieur au seuil,
				 * on republie
				 */
				else if($signalement['statut'] == 'refuse'){
					$table_objet_signale = table_objet_sql($objet_signale);
					$id_table_objet_signale = id_table_objet($objet_signale);
					$statut = sql_getfetsel("statut","$table_objet_signale","$id_table_objet_signale=".intval($id_objet_signale));
					if($statut == 'refuse'){
						include_spip('action/editer_objet');
						$visiteur = $GLOBALS['visiteur_session'];
						$GLOBALS['visiteur_session'] = sql_fetsel('*','spip_auteurs','statut="0minirezo" AND webmestre="oui"');
						objet_instituer($objet_signale,$id_objet_signale,array('statut'=>'publie'));
						$GLOBALS['visiteur_session'] = $visiteur;
						if ($notifications = charger_fonction('notifications', 'inc')) {
							$notifications("depubliersignalement", $id);
						}
					}
				}
			}
		}
	}
	return $flux;
}

?>