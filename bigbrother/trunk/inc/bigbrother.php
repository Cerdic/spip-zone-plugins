<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/session');
include_spip('base/abstract_sql');

// Met à jour la session et enregistre dans la base
function bigbrother_enregistrer_la_visite_du_site(){
	if(($time < ($GLOBALS['visiteur_session']['date_visite'])) OR !($GLOBALS['visiteur_session']['date_visite'])){
		session_set('date_visite', time());
		if($GLOBALS['visiteur_session']['id_auteur']){
			sql_insertq(
				"spip_visites_auteurs",
				array(
					'date' => date('Y-m-d H:i:s', session_get('date_visite')),
					'id_auteur' => $GLOBALS['visiteur_session']['id_auteur']
				)
			);
		}

		$journal = charger_fonction('journal','inc');

		$qui = $GLOBALS['visiteur_session']['nom'] ? $GLOBALS['visiteur_session']['nom'] : $GLOBALS['ip'];
		$qui_ou_ip = $GLOBALS['visiteur_session']['id_auteur'] ? $GLOBALS['visiteur_session']['id_auteur'] : $GLOBALS['ip'];

		$journal(
			_T('bigbrother:action_visite',array('qui' => $qui)),
			array('qui' => $qui_ou_ip,'faire' => 'visite','date' => date('Y-m-d H:i:s', session_get('date_visite')))
		);
	}
}

// Teste s'il faut enregistrer la visite ou pas
function bigbrother_tester_la_visite_du_site(){
	global $visiteur_session;
	/**
	 * Ne pas prendre en compte les bots
	 */
	if (_IS_BOT)
		return;

	// Ne pas tenir compte des tentatives de spam des forums
	if ($_SERVER['REQUEST_METHOD'] !== 'GET'
	OR $_GET['page'] == 'forum')
		return;

	if (isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];
	else if (isset($GLOBALS["HTTP_SERVER_VARS"]["HTTP_REFERER"])) $referer = $GLOBALS["HTTP_SERVER_VARS"]["HTTP_REFERER"];

	// On fait seulement si qqn est connecté
	if((include_spip('inc/session') and session_get('id_auteur') > 0) OR ($ouvert = (lire_config('bigbrother/enregistrer_connexion_anonyme') == 'oui'))){
		include_spip('inc/filtres');
		$time = 0;
		if($ouvert && !intval($visiteur_session['id_auteur'])){
			$time_sql = sql_getfetsel('date','spip_journal','id_auteur='.sql_quote($GLOBALS['ip']));
			if(is_array(recup_date($time_sql))){
				list($annee, $mois, $jour, $heures, $minutes, $secondes) = recup_date($time_sql);
				$time = mktime($heures, $minutes, $secondes, $mois, $jour, $annee);
			}
		}

		// Si la "connexion" n'existe pas on la crée et on enregistre
		if(!$visiteur_session['date_visite']){
			/**
			 * Cas des crons qui ne gardent pas de cookies donc pas de session
			 */
			if($ouvert && !intval($visiteur_session['id_auteur'])){
				if($time < (time()-(30*60))){
					bigbrother_enregistrer_la_visite_du_site();
				}
			}else{
				bigbrother_enregistrer_la_visite_du_site();
			}

		}
		// Sinon si la dernière visite est plus vieille que 30min
		elseif ((time() - $visiteur_session['date_visite']) > (30*60)){
			// On met à jour et en enregistre
			bigbrother_enregistrer_la_visite_du_site();
		}
		// Sinon on ne met que à jour la session
		elseif((time() - $visiteur_session['date_visite']) > 5){
			session_set('date_visite', time());
		}
	}
}

/**
 * Enregistre l'entrée sur un objet
 *
 * Nécessite le placement de la balise #ENREGISTRER_VISITE_AUTEUR
 * dans la boucle de l'objet
 *
 * @param $objet string Le type d'objet : article, rubrique, mot
 * @param $id_objet int L'identifiant numérique de l'objet
 * @param $id_auteur int L'identifiant numérique de l'auteur en cours
 */
function bigbrother_enregistrer_entree($objet, $id_objet, $id_auteur){

	$date_debut = date('Y-m-d H:i:s', time());

	if($objet == 'article' && intval($id_auteur)){
		sql_insertq(
			"spip_visites_articles_auteurs",
			array(
				'id_article' => $id_objet,
				'id_auteur' => $id_auteur,
				'date_debut' => $date_debut
			)
		);
	}

	$journal = charger_fonction('journal','inc');

	$qui = $GLOBALS['visiteur_session']['nom'] ? $GLOBALS['visiteur_session']['nom'] : $GLOBALS['ip'];
	$qui_ou_ip = $GLOBALS['visiteur_session']['id_auteur'] ? $GLOBALS['visiteur_session']['id_auteur'] : $GLOBALS['ip'];

	$infos = array();
	$infos['lang'] = $GLOBALS['contexte']['lang'];

	$journal(
		_T('bigbrother:action_entree_objet',array('qui' => $qui, 'type' => $objet, 'id' => $id_objet)),
		array('qui' => $qui_ou_ip,'faire' => 'visite_entree','quoi' => $objet,'date' => $date_debut,'id' => $id_objet,'infos' => $infos)
	);

	return $date_debut;

}

/**
 * Enregistre la sortie d'un objet
 *
 * Nécessite le placement de la balise #ENREGISTRER_VISITE_AUTEUR
 * dans la boucle de l'objet
 *
 * Cette fonction est appelé en ajax à la sortie de page
 *
 * @param $id_objet int L'identifiant numérique de l'objet
 * @param $objet string Le type d'objet : article, rubrique, mot
 * @param $id_auteur int L'identifiant numérique de l'auteur en cours
 * @param $date_debut datetime La date de la visite à terminer
 */
function bigbrother_enregistrer_sortie($id_objet,$objet, $id_auteur, $date_debut){

	if(!intval($id_objet) OR (!intval($id_auteur) && (lire_config('bigbrother/enregistrer_connexion_anonyme') != 'oui')))
		return false;

	$date_fin = date('Y-m-d H:i:s', time());

	if($objet == 'article'){
		sql_updateq(
			"spip_visites_articles_auteurs",
			array(
				'date_fin' => $date_fin
			),
			"id_article=".intval($id_objet)." AND id_auteur=".intval($id_auteur)." AND date_debut=".sql_quote($date_debut)
		);
	}

	$journal = charger_fonction('journal','inc');

	$qui = $GLOBALS['visiteur_session']['nom'] ? $GLOBALS['visiteur_session']['nom'] : $GLOBALS['ip'];
	$qui_ou_ip = $GLOBALS['visiteur_session']['id_auteur'] ? $GLOBALS['visiteur_session']['id_auteur'] : $GLOBALS['ip'];

	$journal(
		_T('bigbrother:action_sortie_objet',array('qui' => $qui, 'type' => $objet, 'id' => $id_objet)),
		array('qui' => $qui_ou_ip,'faire' => 'visite_entree','quoi' => $objet,'date_debut' => $date_debut,'date_fin' => $date_fin,'id' => $id_objet)
	);
}
?>
