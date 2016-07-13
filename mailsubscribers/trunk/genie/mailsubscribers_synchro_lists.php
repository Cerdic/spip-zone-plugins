<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * CRON de synchro des listes
 *
 * @param $t
 * @return int
 */
function genie_mailsubscribers_synchro_lists_dist($t) {
	include_spip("inc/mailsubscribers");

	$listes = mailsubscribers_listes(array('category' => 'newsletter'));

	// pour chaque liste disponible on inserer un job de synchro (si on trouve la fonction de synchro)
	// pour les traiter separemment les uns des autres si jamais l'un est trop gros
	foreach ($listes as $liste) {
		if (mailsubscribers_trouver_fonction_synchro($liste['id'])) {
			job_queue_add("mailsubscribers_do_synchro_list", "Synchro liste " . $liste['titre'], array($liste['id']),
				"inc/mailsubscribers");
		}
	}

	// les prepa et prop de plus d'1 mois d'anciennete passent a la poubelle
	sql_updateq('spip_mailsubscribers', array('statut' => 'poubelle'),
		sql_in('statut', array('prepa', 'prop')) . " AND date<" . sql_quote(date('Y-m-d H:i:s', strtotime("-1 month"))));

	return 1;
}
