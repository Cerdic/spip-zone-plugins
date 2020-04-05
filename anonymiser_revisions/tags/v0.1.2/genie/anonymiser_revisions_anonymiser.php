<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/sql');

/**
 * Dans l'historique des révisions, hash à intervalle régulier les ip
 * @param int $t le temps depuis l'execution de la dernière tâche
 * @return int positif si réussi
**/
function genie_anonymiser_revisions_anonymiser($t) {
	if (_CNIL_PERIODE) {
		$critere_cnil = 'date<"'.date('Y-m-d', time()-_CNIL_PERIODE).'"'
			. ' AND (id_auteur LIKE "%.%" OR id_auteur LIKE "%:%")'; # ipv4 ou ipv6
		$c = sql_countsel('spip_versions', $critere_cnil);
		if ($c>0) {
			spip_log("CNIL: masquer IP de $c versions anciennes", 'revisions');
			sql_update('spip_versions', array('id_auteur' => 'MD5(id_auteur)'), $critere_cnil);
			return $c;
		}
	}
}
