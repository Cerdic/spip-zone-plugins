<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Pipelines utilisés par le plugins d'Alertes.
 * (NB : celui de notification ralentit de facto le mécanisme de publication d'article)
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/*** Appel des CSS ***/
function alertes_insert_head_css($flux){
	$css = find_in_path("css/alertes.css");
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='".direction_css($css)."' />\n";
	return $flux;
}
/*** Tache CRON pour l'envoie différé des alertes ***/
function alertes_taches_generales_cron($taches_generales){
	//Récuperation de la configuration
	$a = @unserialize($GLOBALS['meta']['config_alertes']);
	if(is_array($a) AND intval($a['intervalle_cron']) > 1){
		$taches_generales['alertes'] = 60 * intval($a['intervalle_cron']); // toutes les X minutes
	}
	return $taches_generales;
}
/*** Alertes : envoie d'email lors de la publication d'un article ***/
function alertes_notifications_destinataires($flux) {
	$quoi = $flux['args']['quoi'];
	$options = $flux['args']['options'];
	$date_pour_envoi = $options['date'];
	//Récuperation de la configuration
	$a = @unserialize($GLOBALS['meta']['config_alertes']);
	//Publication d'article : à ajouter aux alertes des abonnés
	if ($quoi=='instituerarticle' AND $options['statut'] == 'publie' AND is_array($a)){;
		//Seulement si alertes actives
		if($a['activer_alertes'] == 'oui'){
			$emails = array();
			$id_article = $flux['args']['id'];		 
			include_spip('base/abstract_sql');
			//Mots clefs abonnables
			if($a['groupes']){
				$mots = sql_select('mot.id_mot, mot.id_groupe','spip_mots AS mot, spip_mots_liens AS ma','ma.id_objet = '.$id_article.' AND ma.objet ="article" AND ma.id_mot = mot.id_mot AND mot.id_groupe IN('.$a['groupes'].')');
				while ($mot = sql_fetch($mots)){		
					//Qui est abonné à ce mot ?
					$abonnes = sql_select('id_auteur', 'spip_alertes', 'id_objet = '.$mot['id_mot'].' AND objet = "mot"');
					while($abonne = sql_fetch($abonnes)){
						//L'auteur existe-t-il et a-t-il un email ?
						if($verif = sql_select('id_auteur, email', 'spip_auteurs', 'id_auteur= '.$abonne['id_auteur'])){
							while($v = sql_fetch($verif)){
								if( ($v['email'])&&($v['id_auteur']) ){
									//Email, on stocke pour envoi
									$emails[$v['id_auteur']] = $v['email']; //A voir si ça évite les doublons
								}
								if( (!$v['email'])||($v['email'] == '') ){
									//Pas d'email, on enlève des listes
											if($del = sql_delete('spip_alertes', 'id_auteur='.$v['id_auteur'])){
												spip_log('Effacement des auteurs sans emails le '.date('Y-m-d H:i:s'), 'alertes'._LOG_INFO); 
											}					
								}
							}
						}else{
							//Retrait de l'auteur introuvable
							if($del = sql_delete('spip_alertes', 'id_auteur='.$abonne['id_auteur'])){
								spip_log('Effacer des auteurs sans ID le '.date('Y-m-d H:i:s'), 'alertes'._LOG_INFO); 
							}
						}
					}
				}
			}
			//Fin Mots-clefs abonnables
			
			//Secteurs abonnables
			if($a['secteurs']){
				$secteurs = sql_select('id_secteur','spip_articles','id_article = '.$id_article.'  AND id_secteur IN('.$a['secteurs'].')');
				while ($secteur = sql_fetch($secteurs)){		
					//Qui est abonné à ce secteur ? 
					$abonnes = sql_select('id_auteur', 'spip_alertes', 'id_objet = '.$secteur['id_secteur'].' AND objet = "secteur"');
					while($abonne = sql_fetch($abonnes)){
						//L'auteur existe-t-il et a-t-il un email ?
						if($verif = sql_select('id_auteur, email', 'spip_auteurs', 'id_auteur= '.$abonne['id_auteur'])){
							while($v = sql_fetch($verif)){
								if( ($v['email'])&&($v['id_auteur']) ){
									//Email, on stocke pour envoi
									$emails[$v['id_auteur']] = $v['email']; //A voir si ça évite les doublons
								}
								if( (!$v['email'])||($v['email'] == '') ){
									//Pas d'email, on enlève des listes
											if($del = sql_delete('spip_alertes', 'id_auteur='.$v['id_auteur'])){
												spip_log('Effacer des auteurs sans email le '.date('Y-m-d H:i:s'), 'alertes'._LOG_INFO); 
											}					
								}
							}
						}else{
							//Retrait de l'auteur introuvable
							if($del = sql_delete('spip_alertes', 'id_auteur='.$abonne['id_auteur'])){
								spip_log('Effacer des auteurs sans ID le '.date('Y-m-d H:i:s'), 'alertes'._LOG_INFO); 
							}
						}
					}
				}
			}
			//Fin secteurs abonnables
			
			//Rubriques abonnables
			if($a['rubriques']){
				$rubriques = sql_select('id_rubrique','spip_articles','id_article = '.$id_article.'  AND id_rubrique IN('.$a['rubriques'].')');
				while ($rubrique = sql_fetch($rubriques)){		
					//Qui est abonné à cette rubrique ?
					$abonnes = sql_select('id_auteur', 'spip_alertes', 'id_objet = '.$rubrique['id_rubrique'].' AND objet = "rubrique"');
					while($abonne = sql_fetch($abonnes)){
						//L'auteur existe-t-il et a-t-il un email ?
						if($verif = sql_select('id_auteur, email', 'spip_auteurs', 'id_auteur= '.$abonne['id_auteur'])){
							while($v = sql_fetch($verif)){
								if( ($v['email'])&&($v['id_auteur']) ){
									//Email, on stocke pour envoi
									$emails[$v['id_auteur']] = $v['email']; //A voir si ça évite les doublons
								}
								if( (!$v['email'])||($v['email'] == '') ){
									//Pas d'email, on enlève des listes
											if($del = sql_delete('spip_alertes', 'id_auteur='.$v['id_auteur'])){
												spip_log('Effacer des auteurs sans email le '.date('Y-m-d H:i:s'), 'alertes'._LOG_INFO); 
											}					
								}
							}
						}else{
							//Retrait de l'auteur introuvable
							if($del = sql_delete('spip_alertes', 'id_auteur='.$abonne['id_auteur'])){
								spip_log('Effacer des auteurs sans ID le '.date('Y-m-d H:i:s'), 'alertes'._LOG_INFO); 
							}
						}
					}
				}
			}
			//Fin rubriques abonnables

			//Auteurs abonnables
			if($a['auteurs']){
				$auteurs = sql_select("id_auteur","spip_auteurs_liens","id_objet = ".$id_article."  AND objet='article'");
				while ($auteur = sql_fetch($auteurs)){		
					//Qui est abonné à cet auteur ?
					$abonnes = sql_select('id_auteur', 'spip_alertes', 'id_objet = '.$auteur['id_auteur'].' AND objet = "auteur"');
					while($abonne = sql_fetch($abonnes)){
						//L'auteur existe-t-il et a-t-il un email ?
						if($verif = sql_select('id_auteur, email', 'spip_auteurs', 'id_auteur= '.$abonne['id_auteur'])){
							while($v = sql_fetch($verif)){
								if( ($v['email'])&&($v['id_auteur']) ){
									//Email, on stocke pour envoi
									$emails[$v['id_auteur']] = $v['email']; //A voir si ça évite les doublons
								}
								if( (!$v['email'])||($v['email'] == '') ){
									//Pas d'email, on enlève des listes
											if($del = sql_delete('spip_alertes', 'id_auteur='.$v['id_auteur'])){
												spip_log('Effacer des auteurs sans email le '.date('Y-m-d H:i:s'), 'alertes'._LOG_INFO); 
											}					
								}
							}
						}else{
							//Retrait de l'auteur introuvable
							if($del = sql_delete('spip_alertes', 'id_auteur='.$abonne['id_auteur'])){
								spip_log('Effacer des auteurs sans ID le '.date('Y-m-d H:i:s'), 'alertes'._LOG_INFO); 
							}
						}
					}
				}
			}
			//Fin auteurs abonnables
			
			//Maintenant, on gére l'envoi
			if( is_array($emails) AND sizeof($emails) > 0 ){
				//Mode CRON ou direct ? Mode CRON d'office si la configuration autorise les articles publié post-daté.
				if( ($a['mode_envoi'] == 'cron')||($GLOBALS['meta']['post_dates'] == 'oui') ){
					//Mode CRON : on enregistre tout ça dans la table d'envois CRON dediée
					foreach($emails as $id_auteur => $email){
						$ins_cron =  sql_insertq('spip_alertes_cron', array('id_auteur'=> $id_auteur, 'id_objet' => $id_article, 'objet' => 'article', 'date_pour_envoi' => $date_pour_envoi));
					}
				
				}else{
				//Mode direct (attention à la charge)
					//Fonctions facteurs
					include_spip('classes/facteur'); //dépendance Facteur
					$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
						
					foreach($emails as $id_auteur => $email){		
						//On construit le mail à partir de templates
						$header_email = recuperer_fond("alertes/header-email-alerte",  array('id_article' => $id_article,'id_auteur' => $id_auteur));
						$footer_email = recuperer_fond("alertes/footer-email-alerte",  array('id_article' => $id_article,'id_auteur' => $id_auteur));
						$corps_email = recuperer_fond("alertes/corps-email-alerte", array('id_article' => $id_article,'id_auteur' => $id_auteur));
						$sujet =  recuperer_fond("alertes/sujet-email-alerte", array('id_article' => $id_article,'id_auteur' => $id_auteur));  // Sujet du mail aussi en template (dangereux mais pratique si on veut le customiser). Doit renvoyer du texte brut				
						//On n'envoie que si on a un contenu (présumé dans le corps du mail
						if($corps_email){
							//Envoi email via Facteur
							$html = $header_email.$corps_email.$footer_email;
							$texte = Facteur::html2text($html); //Version  texte
							$corps = array(
								'html' => $html,
								'texte' => $texte
							);	
							if($ok = $envoyer_mail($email, $sujet, $corps)){
								//Email envoyé. On log.
								spip_log('Email correctement envoyer a '.$email, 'alertes'._LOG_INFO); 
							}else{
								//Email non envoyé. On log.
								spip_log('Echec de l\'envoie d\'email a '.$email, 'alertes'._LOG_ERREUR); 
							}
						}					
					}
				}
			}
		}
	}
	return $flux;
}


?>