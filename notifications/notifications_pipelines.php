<?php
/*
 * Plugin Notifications
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */


/**
 * Ajouter des destinataires dans une notification en lot
 *
 * @param array $flux
 * @return array
 */
function notifications_notifications_destinataires($flux) {
	$quoi = $flux['args']['quoi'];
	if ($quoi=='instituer_article'
	  AND $GLOBALS['notifications']['prevenir_auteurs_articles']){
		$id_article = $flux['args']['id'];
		$options = $flux['args']['options'];

		include_spip('base/abstract_sql');

		// Qui va-t-on prevenir en plus ?
		$result_email = sql_select("auteurs.email", "spip_auteurs AS auteurs, spip_auteurs_articles AS lien", "lien.id_article=".intval($id_article)." AND auteurs.id_auteur=lien.id_auteur");

		while ($qui = sql_fetch($result_email)) {
			$flux['data'][] = $qui['email'];
		}

	}
	
	return $flux;
}
?>