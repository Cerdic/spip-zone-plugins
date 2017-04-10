<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Formulaires CVT : Patrice Vanneufville, 2012     #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GNU/GPL (c) 2012                       #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Charger : declarer les champs postes et y integrer les valeurs par defaut ou precedemment postees
 *
 * @param int $id_jeu
 * @return array
 */
function formulaires_jeu_charger_dist($id_jeu = 0){
	// si index_jeu = 0, premier affichage et pas de correction
	$indexJeux = intval(_request('index_jeux'));
	return array(
		// si id_jeu = 0, pas de formulaire possible
		'id_jeu' => $id_jeu,
		// index aleatoire du premier jeu de la page
		'debut_index_jeux' => intval(_request('debut_index_jeux')),
		// index aleatoire du jeu courant
		'index_jeux' => $indexJeux,
		// champ 0/1 indiquant s'il faut corriger ou non
		'correction'.$indexJeux => intval(_request('correction_'.$indexJeux)),
		// l'ensemble des reponses postees
		'reponses'.$indexJeux => _request('reponses'.$indexJeux),
	);
}

/**
 * Verifier : corriger les champs postes
 *
 * @param int $id_jeu
 * @return array
 */
function formulaires_jeu_verifier_dist($id_jeu = 0){
	// pas de gestion d'erreur bloquante pour l'instant
	return array(
		// erreurs a notifier
	);
}

/**
 * Traiter : stocker les resultats en base
 *
 * @param int $id_jeu
 * @return array
 */
function formulaires_jeu_traiter_dist($id_jeu = 0){    
	// les resultats sont deja enregistres par la correction du jeu
	return array(
//		'message_ok' => _L('config_info_enregistree'),
	);
}


?>