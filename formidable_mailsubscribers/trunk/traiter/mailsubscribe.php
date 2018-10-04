<?php
/**
 * Traitement abonnement à des listes de diffusion à la saisie d'un formulaire
 *
 * @plugin     Formidable : abonnement à des listes de diffusion
 * @copyright  2017
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\FormidableMailsubscribers\traiter\mailsubscribe
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Traitements
 *
 * @note
 * Il reste quelques options non utilisées pour la fonction d'abonnement :
 * - lang : code de langue
 * - force : true pour bypasser le doubleoptin, -1 pour le forcer
 * - graceful : false = ne pas inscrire un auteur désabonné
 *
 * @param array $args
 * @param bool $retours
 * @return bool
 */
function traiter_mailsubscribe_dist($args, $retours){

	$formulaire  = $args['formulaire'];
	$options     = $args['options'];
	$saisies     = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);

	// Récupérons les noms des champs configurés
	$champ_email = isset($options['champ_email_mailsubscribe']) ? $options['champ_email_mailsubscribe'] : null;
	$champ_nom = isset($options['champ_nom_mailsubscribe']) ? $options['champ_nom_mailsubscribe'] : null;
	$champ_listes = isset($options['champ_listes_mailsubscribe']) ? $options['champ_listes_mailsubscribe'] : null;

	// Il faut au minimum un email pour procéder
	if ($email = _request($champ_email)) {

		// Les options à transmettre à la fonction d'abonnement
		$options_subscribe = array();
		// 1) options : valeurs saisies par l'utilisateur
		if ($nom = _request($champ_nom)){
			$options_subscribe['nom'] = $nom;
		}
		if ($listes = _request($champ_listes)){
			$options_subscribe['listes'] = $listes;
		}
		// 2) options : valeurs configurées dans les traitements
		if (isset($options['notify'])
			and $notify = $options['notify']
		){
			$options_subscribe['notify'] = true;
		}

		// Go go go
		$subscribe = charger_fonction('subscribe', 'newsletter');
		$subscribe($email, $options_subscribe);
	}

	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['newsletters'] = true;

	return $retours;
}
