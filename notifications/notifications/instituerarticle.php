<?php
/*
 * Plugin Notifications
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */


// Fonction appelee par divers pipelines
// http://doc.spip.org/@notifications_instituerarticle_dist
function notifications_instituerarticle_dist($quoi, $id_article, $options) {

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange");
		return;
	}

	include_spip('inc/texte');

	if ($options['statut'] == 'publie')
		notifier_publication_article($id_article);

	if ($options['statut'] == 'prop' AND $options['statut_ancien'] != 'publie')
		notifier_proposition_article($id_article);
}

?>