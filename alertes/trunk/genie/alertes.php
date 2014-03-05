<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Système d'envoi des emails d'alertes par pseudo-CRON SPIP.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_alertes_dist($time) {
	include_spip('base/abstract_sql');
	$now = date('Y-m-d H:i:s');
	//Récupération de la configuration
	$a = @unserialize($GLOBALS['meta']['config_alertes']);
	$limit = "0, ".intval($a['nb_mails']);
	$nb_max = sql_countsel("id_alerte_cron", "spip_alertes_cron","date_pour_envoi <= '".$now."'"); //Nombres total d'alerte à traité, donc de mail à envoyer.
	//Si on a plus d'alertes prevues que d'email autorisé à envoyer, il faudra poursuivre la tâche.
	if($nb_max > intval($a['nb_mails'])){
		$time = - intval($a['nb_mails']); //Si j'ai bien compris, temps en seconde avant de relancer la tâche non-terminée.
	}
	//Parcours des alertes demandées, qui ne doivent pas être dans le futur
	if ($resultats = sql_select("*", "spip_alertes_cron","date_pour_envoi <= '".$now."'",$groupby, $orderby, $limit)) {
		include_spip('classes/facteur'); //dépendance Facteur
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
		//Envoi des emails d'alertes
		while ($res = sql_fetch($resultats)) {
			$id_article = $res['id_objet']; //Pour l'instant ça ne gère que les articles
			$id_auteur = $res['id_auteur'];
			//On récupere l'email de l'auteur concerné
			if($auteur = sql_select('id_auteur,email','spip_auteurs','id_auteur = '.intval($id_auteur)) ){
				while ($aut = sql_fetch($auteur)) {
					//Evidemment, il faut l'email du membre.
					$email = $aut['email'];
					//On va quand même vérifier que l'article existe encore/est en etat publié
					$verif = sql_select('statut','spip_articles','id_article = '.intval($id_article));
					while($art = sql_fetch($verif)){
						$statut = $art['statut'];
					}
					if( ($email)&&($statut == 'publie') ){
						//On build le mail à partir de templates
						$header_email = recuperer_fond("alertes/header-email-alerte",  array('id_article' => $id_article,'id_auteur' => $id_auteur));
						$footer_email = recuperer_fond("alertes/footer-email-alerte",  array('id_article' => $id_article,'id_auteur' => $id_auteur));
						$corps_email = recuperer_fond("alertes/corps-email-alerte", array('id_article' => $id_article,'id_auteur' => $id_auteur));
						$sujet =  recuperer_fond("alertes/sujet-email-alerte", array('id_article' => $id_article,'id_auteur' => $id_auteur));  // Sujet du mail aussi en template (dangereux mais pratique si on veut le customiser). Doit renvoyer du texte brut				
						//On n'envoie que si on a un contenu (présumé dans le corps du mail)
						if($corps_email){
							//Envoi email via Facteur
							$html = $header_email.$corps_email.$footer_email;
							$texte = Facteur::html2text($html); //Version  texte
							$corps = array(
								'html' => $html,
								'texte' => $texte
							);	
							if($ok = $envoyer_mail($email, $sujet, $corps)){
								//Email envoyé, on retire l'alerte-cron et on log.
								$del = sql_delete('spip_alertes_cron', 'id_alerte_cron = ' . intval($res['id_alerte_cron']));
								spip_log('Email correctement envoyer a '.$email, 'alertes'._LOG_INFO); 
							}else{
								//Email non envoyé. On log.
								spip_log('Echec de l\'envoie d\'email a '.$email, 'alertes'._LOG_ERREUR); 
							}
						}
					}else{
						//Auteur sans email ou article non-publié/inexistant
						if($statut != 'publie'){
							//Article non publie, l'alerte n'a pas lieu d'être nulle part
							$del = sql_delete("spip_alertes_cron", "objet = 'article' AND id_objet = " . intval($id_article));
						}
					}
				}
			}
		}
	}
	
	return $time;
}

?>
